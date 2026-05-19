#!/usr/bin/env bash
set -e

cp -n .env.example .env || true
npm install
npm run prisma:generate
npm run prisma:migrate
npm run db:seed

echo "Local setup complete."
