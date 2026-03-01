# Interaction Tracking Platform

This repository contains a modular stack for a backend admin panel, Android interaction tracking agent, and a React admin dashboard.

## Project Structure

```
backend/   FastAPI API + WebSocket server
frontend/  React + Tailwind admin dashboard
android/   Android (Kotlin) agent with AccessibilityService
```

## Backend (FastAPI)

### Setup

```bash
cd backend
python -m venv .venv
source .venv/bin/activate
pip install -r requirements.txt
cp .env.example .env
```

### Run

```bash
uvicorn app.main:app --reload --port 8000
```

### Endpoints

- `POST /api/admin/login` (token exchange, HTTP Bearer)
- `GET /api/devices`
- `GET /api/events` (filters: `device_id`, `app`, `type`, `start`, `end`)
- `WS /ws/device/{device_id}?api_key=...`
- `WS /ws/admin?token=...`

## Frontend (React)

### Setup

```bash
cd frontend
npm install
cp .env.example .env
npm run dev
```

## Android Agent

### Setup

Open `android/` in Android Studio and sync Gradle.

- Ensure the backend is reachable (edit `WebSocketManager.kt` for the server IP).
- Install the app and enable the Accessibility Service from the main screen.
- The foreground service starts automatically to keep the WebSocket alive.
- Payloads include `package_name`, `event_type`, `content_description`, and bounds metadata for overlays.

## Security Notes

- Always rotate `ADMIN_TOKEN` and `DEVICE_KEYS` with environment variables.
- Terminate WebSocket connections on invalid tokens (handled server-side).
- Deploy behind HTTPS/WSS with a reverse proxy (NGINX/Caddy).

## Deployment Guide (VPS + Docker)

1. Provision a VPS with Docker and Docker Compose.
2. Create environment files for backend and frontend.
3. Run Postgres, backend, and frontend in a compose stack.
4. Terminate TLS with a reverse proxy and forward `/ws` to FastAPI.

Example Docker Compose skeleton:

```yaml
services:
  db:
    image: postgres:15
    environment:
      POSTGRES_PASSWORD: postgres
      POSTGRES_DB: interaction
  backend:
    build: ./backend
    environment:
      DATABASE_URL: postgresql+asyncpg://postgres:postgres@db:5432/interaction
    depends_on:
      - db
  frontend:
    build: ./frontend
```
