from __future__ import annotations

from functools import lru_cache
from typing import List

from pydantic import BaseSettings, Field


class Settings(BaseSettings):
    database_url: str = Field("postgresql+asyncpg://postgres:postgres@localhost:5432/interaction", env="DATABASE_URL")
    admin_token: str = Field("admin-token", env="ADMIN_TOKEN")
    device_keys: str = Field("device-123:device-key", env="DEVICE_KEYS")
    allowed_origins: List[str] = Field(["http://localhost:5173"], env="ALLOWED_ORIGINS")


@lru_cache(maxsize=1)
def get_settings() -> Settings:
    return Settings()
