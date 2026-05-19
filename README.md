# CRUD Admin Panel Backend (Express + Prisma)

Production-ready starter admin panel inspired by common `/mimin` dashboards.

## Features
- Session auth (login/logout) with secure cookie settings
- Role & permission management (RBAC)
- Dashboard analytics
- CRUD modules starter: users, roles, products, categories, settings
- Search/filter/pagination
- Request validation (Zod)
- Rate limiting for login endpoint
- REST API endpoints
- Prisma migrations + seed
- Activity log + audit fields
- Health checks (`/healthz`, `/readyz`)

## Environment
Copy and edit env:

```bash
cp .env.example .env
```

Required values:
- `DATABASE_URL`
- `SESSION_SECRET` (strong random secret)
- `NODE_ENV` (`production` in prod)
- `TRUST_PROXY=true` when behind reverse proxy

## Quick Start (Local)
```bash
npm install
npm run prisma:generate
npm run prisma:migrate
npm run db:seed
npm run dev
```

Open: `http://localhost:3000`

Default credentials:
- admin@example.com / Admin123!
- manager@example.com / Manager123!

## Production Deploy Checklist
1. Use PostgreSQL/MySQL for production DB.
2. Run `npm ci`.
3. Run `npm run prisma:generate`.
4. Run `npm run prisma:migrate:deploy`.
5. Set `NODE_ENV=production`.
6. Set strong `SESSION_SECRET`.
7. Enable HTTPS and set `TRUST_PROXY=true` when applicable.
8. Start with `npm run start`.

## Optional ZIP build (not tracked in git)
```bash
npm run zip
```
