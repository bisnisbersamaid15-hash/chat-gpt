from __future__ import annotations

from typing import Dict


def parse_device_keys(raw: str) -> Dict[str, str]:
    pairs: Dict[str, str] = {}
    for entry in raw.split(","):
        if not entry:
            continue
        device_id, _, key = entry.partition(":")
        if device_id and key:
            pairs[device_id] = key
    return pairs


def validate_device_key(device_id: str, key: str, raw: str) -> bool:
    pairs = parse_device_keys(raw)
    return pairs.get(device_id) == key


def decode_admin_token(token: str, expected: str) -> bool:
    return token == expected
