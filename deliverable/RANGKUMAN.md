# Ringkasan Proyek: Interaction Tracking Platform

Folder ini berisi ringkasan singkat dari seluruh kode pada repo agar mudah diunduh sebagai satu paket.

## Struktur Utama

```
backend/   FastAPI API + WebSocket server
frontend/  React + Tailwind admin dashboard
android/   Android (Kotlin) agent dengan AccessibilityService
```

## Backend (FastAPI)

- **Auth & Security**: token admin `ADMIN_TOKEN` dan API key per device (`DEVICE_KEYS`).
- **WebSocket**:
  - `/ws/device/{device_id}` menerima event interaksi dari agent.
  - `/ws/admin` broadcast event realtime ke admin.
- **REST API**:
  - `GET /api/devices` daftar device online/offline + IP + last seen.
  - `GET /api/events` filter events (device, app, type, range waktu).
- **Database**: PostgreSQL dengan tabel `interaction_events`.

## Frontend (React + Tailwind)

- **Dashboard Admin**:
  - Ringkasan device online dan total event.
  - Device list dengan search + status badge + IP + last seen.
  - Live events table (termasuk `content_description`).
  - Interaction overlay view untuk bounds interaksi.

## Android Agent (Kotlin)

- **AccessibilityService** menangkap event:
  - TYPE_VIEW_CLICKED, TYPE_VIEW_LONG_CLICKED, TYPE_VIEW_SCROLLED,
    TYPE_WINDOW_STATE_CHANGED, TYPE_VIEW_TEXT_CHANGED.
- Mengirim JSON via WebSocket berisi:
  - `device_id`, `package_name`, `event_type`, `text`, `content_description`,
    `view_id`, bounds `{l,t,r,b}`, `ts` (ms).
- **Foreground Service** agar tetap hidup.

## Cara Jalan Singkat

- Backend:
  - `cd backend`
  - `pip install -r requirements.txt`
  - `uvicorn app.main:app --reload --port 8000`
- Frontend:
  - `cd frontend`
  - `npm install`
  - `npm run dev`
- Android:
  - Buka `android/` di Android Studio, edit IP backend di `WebSocketManager.kt`.

## Catatan Keamanan

- Gunakan HTTPS/WSS di production.
- Ganti `ADMIN_TOKEN` dan `DEVICE_KEYS` melalui env.

