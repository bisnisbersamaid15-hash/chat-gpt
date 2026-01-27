from __future__ import annotations

from dataclasses import dataclass, field
from datetime import datetime
from typing import Any, Dict, List

from fastapi import WebSocket


@dataclass
class DeviceConnection:
    device_id: str
    websocket: WebSocket
    connected_at: datetime = field(default_factory=datetime.utcnow)
    last_seen: datetime = field(default_factory=datetime.utcnow)
    ip: str | None = None


class DeviceHub:
    def __init__(self) -> None:
        self._devices: Dict[str, DeviceConnection] = {}

    async def connect(self, device_id: str, websocket: WebSocket) -> None:
        await websocket.accept()
        ip = websocket.client.host if websocket.client else None
        self._devices[device_id] = DeviceConnection(device_id=device_id, websocket=websocket, ip=ip)

    def disconnect(self, device_id: str) -> None:
        self._devices.pop(device_id, None)

    def mark_seen(self, device_id: str) -> None:
        connection = self._devices.get(device_id)
        if connection:
            connection.last_seen = datetime.utcnow()

    def snapshot(self) -> List[Dict[str, Any]]:
        now = datetime.utcnow()
        payload = []
        for connection in self._devices.values():
            status = "online" if (now - connection.last_seen).seconds < 30 else "offline"
            payload.append(
                {
                    "device_id": connection.device_id,
                    "status": status,
                    "last_seen": connection.last_seen.isoformat(),
                    "ip": connection.ip,
                }
            )
        return payload


class AdminHub:
    def __init__(self) -> None:
        self._admins: List[WebSocket] = []

    async def connect(self, websocket: WebSocket) -> None:
        await websocket.accept()
        self._admins.append(websocket)

    def disconnect(self, websocket: WebSocket) -> None:
        if websocket in self._admins:
            self._admins.remove(websocket)

    async def broadcast(self, message: Dict[str, Any]) -> None:
        for ws in list(self._admins):
            try:
                await ws.send_json(message)
            except Exception:
                self.disconnect(ws)
