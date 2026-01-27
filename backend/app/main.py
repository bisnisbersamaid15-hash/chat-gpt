from __future__ import annotations

from datetime import datetime, timezone
from typing import Any, Dict, List, Optional, Union

from fastapi import Depends, FastAPI, HTTPException, WebSocket, WebSocketDisconnect
from fastapi.middleware.cors import CORSMiddleware
from fastapi.security import HTTPAuthorizationCredentials, HTTPBearer
from sqlalchemy import select
from sqlalchemy.ext.asyncio import AsyncSession

from .config import Settings
from .database import get_session, init_db
from .models import InteractionEvent
from .schemas import EventFilters, InteractionEventIn, InteractionEventOut
from .security import decode_admin_token, validate_device_key
from .ws import AdminHub, DeviceHub

settings = Settings()
app = FastAPI(title="Interaction Tracking Backend")

app.add_middleware(
    CORSMiddleware,
    allow_origins=settings.allowed_origins,
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

security = HTTPBearer()
admin_hub = AdminHub()
device_hub = DeviceHub()


@app.on_event("startup")
async def startup() -> None:
    await init_db()


@app.get("/health")
async def health() -> Dict[str, str]:
    return {"status": "ok"}


@app.get("/api/devices")
async def list_devices() -> List[Dict[str, Any]]:
    return device_hub.snapshot()


@app.get("/api/events", response_model=List[InteractionEventOut])
async def list_events(
    filters: EventFilters = Depends(),
    session: AsyncSession = Depends(get_session),
) -> List[InteractionEventOut]:
    query = select(InteractionEvent)
    if filters.device_id:
        query = query.where(InteractionEvent.device_id == filters.device_id)
    if filters.app:
        query = query.where(InteractionEvent.app == filters.app)
    if filters.type:
        query = query.where(InteractionEvent.type == filters.type)
    if filters.start_time:
        query = query.where(InteractionEvent.ts >= filters.start_time)
    if filters.end_time:
        query = query.where(InteractionEvent.ts <= filters.end_time)
    query = query.order_by(InteractionEvent.ts.desc()).limit(filters.limit).offset(filters.offset)
    result = await session.execute(query)
    return [InteractionEventOut.from_orm(row) for row in result.scalars().all()]


def _parse_timestamp(value: Optional[Union[int, datetime]]) -> datetime:
    if value is None:
        return datetime.utcnow()
    if isinstance(value, int):
        return datetime.fromtimestamp(value / 1000, tz=timezone.utc).replace(tzinfo=None)
    return value


@app.websocket("/ws/device/{device_id}")
async def device_socket(
    websocket: WebSocket,
    device_id: str,
    session: AsyncSession = Depends(get_session),
) -> None:
    key = websocket.query_params.get("api_key")
    if not key or not validate_device_key(device_id, key, settings.device_keys):
        await websocket.close(code=1008)
        return
    await device_hub.connect(device_id, websocket)
    try:
        while True:
            payload = await websocket.receive_json()
            event_in = InteractionEventIn(**payload)
            app_name = event_in.app or event_in.package_name
            event_type = event_in.type or event_in.event_type
            if not app_name or not event_type:
                continue
            record = InteractionEvent(
                device_id=device_id,
                ts=_parse_timestamp(event_in.ts),
                app=app_name,
                type=event_type,
                text=event_in.text,
                content_description=event_in.content_description,
                view_id=event_in.view_id,
                l=event_in.l,
                t=event_in.t,
                r=event_in.r,
                b=event_in.b,
            )
            session.add(record)
            await session.commit()
            await session.refresh(record)
            device_hub.mark_seen(device_id)
            await admin_hub.broadcast(
                {
                    "device_id": device_id,
                    "ts": record.ts.isoformat(),
                    "app": record.app,
                    "type": record.type,
                    "text": record.text,
                    "content_description": record.content_description,
                    "view_id": record.view_id,
                    "l": record.l,
                    "t": record.t,
                    "r": record.r,
                    "b": record.b,
                }
            )
    except WebSocketDisconnect:
        device_hub.disconnect(device_id)


@app.websocket("/ws/admin")
async def admin_socket(websocket: WebSocket) -> None:
    token = websocket.query_params.get("token")
    if not token or not decode_admin_token(token, settings.admin_token):
        await websocket.close(code=1008)
        return
    await admin_hub.connect(websocket)
    try:
        while True:
            await websocket.receive_text()
    except WebSocketDisconnect:
        admin_hub.disconnect(websocket)


@app.post("/api/admin/login")
async def admin_login(
    credentials: HTTPAuthorizationCredentials = Depends(security),
) -> Dict[str, str]:
    if credentials.credentials != settings.admin_token:
        raise HTTPException(status_code=401, detail="Invalid admin token")
    return {"token": settings.admin_token}
