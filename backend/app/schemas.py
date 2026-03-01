from __future__ import annotations

from datetime import datetime
from typing import Optional

from pydantic import BaseModel, Field


class InteractionEventIn(BaseModel):
    app: Optional[str] = None
    type: Optional[str] = None
    package_name: Optional[str] = None
    event_type: Optional[str] = None
    text: Optional[str] = None
    content_description: Optional[str] = None
    view_id: Optional[str] = None
    l: Optional[int] = None
    t: Optional[int] = None
    r: Optional[int] = None
    b: Optional[int] = None
    ts: Optional[datetime | int] = None


class InteractionEventOut(BaseModel):
    id: int
    device_id: str
    ts: datetime
    app: str
    type: str
    text: Optional[str] = None
    content_description: Optional[str] = None
    view_id: Optional[str] = None
    l: Optional[int] = None
    t: Optional[int] = None
    r: Optional[int] = None
    b: Optional[int] = None

    class Config:
        orm_mode = True


class EventFilters(BaseModel):
    device_id: Optional[str] = None
    app: Optional[str] = None
    type: Optional[str] = None
    start_time: Optional[datetime] = Field(None, alias="start")
    end_time: Optional[datetime] = Field(None, alias="end")
    limit: int = 200
    offset: int = 0
