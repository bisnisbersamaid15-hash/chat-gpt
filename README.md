# CRUD Admin Panel Backend (Express + Prisma)

Production-ready starter admin panel inspired by common `/mimin` dashboards.

## Features
- Session auth (login/logout)
- Role & permission management (RBAC)
- Dashboard analytics
- CRUD modules: users, roles, products, categories, settings
- Search/filter/pagination
- Form validation (Zod)
- File/image upload (Multer)
- REST API endpoints
- Prisma migrations + seed
- Activity log + audit fields
- i18n-ready middleware hook
- Dark-mode capable UI via AdminLTE-compatible layout

## Quick Start
```bash
cp .env.example .env
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

## Build ZIP
```bash
bash install.sh
```
It also generates `crud-admin-panel.zip`.
