from __future__ import annotations

from datetime import datetime

from sqlalchemy import DateTime, Integer, String
from sqlalchemy.orm import Mapped, mapped_column

from .database import Base


class InteractionEvent(Base):
    __tablename__ = "interaction_events"

    id: Mapped[int] = mapped_column(Integer, primary_key=True, index=True)
    device_id: Mapped[str] = mapped_column(String(128), index=True)
    ts: Mapped[datetime] = mapped_column(DateTime, index=True)
    app: Mapped[str] = mapped_column(String(255), index=True)
    type: Mapped[str] = mapped_column(String(128), index=True)
    text: Mapped[str | None] = mapped_column(String(1024), nullable=True)
    content_description: Mapped[str | None] = mapped_column(String(1024), nullable=True)
    view_id: Mapped[str | None] = mapped_column(String(255), nullable=True)
    l: Mapped[int | None] = mapped_column(Integer, nullable=True)
    t: Mapped[int | None] = mapped_column(Integer, nullable=True)
    r: Mapped[int | None] = mapped_column(Integer, nullable=True)
    b: Mapped[int | None] = mapped_column(Integer, nullable=True)
