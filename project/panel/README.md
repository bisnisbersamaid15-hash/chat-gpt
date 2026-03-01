# Backend Panel (SSR PHP + Bootstrap + jQuery)

## Architecture

| Layer        | Technology                                    |
|-------------|-----------------------------------------------|
| Server      | PHP 8.5 built-in dev server (SSR)             |
| Database    | SQLite via PDO (auto-created on first run)    |
| Frontend    | Bootstrap 5.3.3 + Bootstrap Icons 1.11.3      |
| JS          | jQuery 3.7.1 (AJAX, DOM manipulation)         |
| Theme       | Custom `operator.css` — black & gold          |

## Structure

```
panel/
├── index.php              ← Login (DB auth, CSRF, rate limiting)
├── dashboard.php          ← KPI from DB aggregates + latest orders
├── users.php              ← Full CRUD: list, add, edit, delete
├── products.php           ← Full CRUD: cards, add, edit, delete
├── orders.php             ← Full CRUD: table, filter, add, edit, delete
├── reports.php            ← Paid/Pending/Failed volume + real CSV export
├── settings.php           ← Brand/notification config + audit log viewer
├── logout.php             ← Session cleanup + audit log
├── api/
│   ├── add-user.php       ← POST: create user (CSRF + validation)
│   ├── update-user.php    ← POST: update user
│   ├── delete-user.php    ← POST: delete user
│   ├── add-product.php    ← POST: create product
│   ├── update-product.php ← POST: update product
│   ├── delete-product.php ← POST: delete product
│   ├── add-order.php      ← POST: create order
│   ├── update-order.php   ← POST: update order
│   ├── delete-order.php   ← POST: delete order
│   ├── orders.php         ← GET: list orders (JSON)
│   ├── settings.php       ← POST: save settings
│   └── export-csv.php     ← GET: download orders CSV
├── assets/
│   ├── css/operator.css   ← Dark theme (--op-bg:#0f0f11, --op-gold:#d4af37)
│   └── js/panel.js        ← jQuery CRUD handlers + CSRF setup
├── data/
│   ├── database.php       ← PDO singleton, migration, seed, helpers
│   ├── panel.sqlite       ← Auto-generated database file
│   └── seed.php           ← Legacy static data (no longer used)
└── partials/
    ├── bootstrap.php      ← Session config, DB init, guards, helpers
    ├── layout-top.php     ← HTML head, sidebar, CSRF meta tag
    └── layout-bottom.php  ← JS includes, closing tags
```

## Security Features

- **Authentication**: Database-backed with `password_hash()` / `password_verify()`
- **CSRF Protection**: Token in meta tag + `X-CSRF-Token` header for AJAX
- **Rate Limiting**: 5 failed login attempts → lockout for 5 minutes per IP
- **Session Hardening**: `session_regenerate_id()`, `HttpOnly`, `SameSite=Strict`
- **API Guards**: All endpoints require authentication (401) + method enforcement (405)
- **Input Validation**: Email format, role/status whitelists, length limits
- **XSS Prevention**: `htmlspecialchars()` on all user-facing output
- **Audit Logging**: All CRUD operations + login/logout tracked with IP

## Run Locally

```bash
cd project
php -S 0.0.0.0:8080
```

Then open `http://localhost:8080/panel/index.php`.

**Demo credentials:**
- Email: `mnk@gmail.com`
- Password: `aaa123`

## API Endpoints

All POST endpoints require authentication + CSRF token via `X-CSRF-Token` header.

| Method | Endpoint                  | Description          |
|--------|--------------------------|----------------------|
| GET    | `/panel/api/orders.php`  | List all orders JSON |
| GET    | `/panel/api/export-csv.php` | Download orders CSV |
| POST   | `/panel/api/add-user.php` | Create user         |
| POST   | `/panel/api/update-user.php` | Update user       |
| POST   | `/panel/api/delete-user.php` | Delete user       |
| POST   | `/panel/api/add-product.php` | Create product    |
| POST   | `/panel/api/update-product.php` | Update product |
| POST   | `/panel/api/delete-product.php` | Delete product |
| POST   | `/panel/api/add-order.php` | Create order       |
| POST   | `/panel/api/update-order.php` | Update order     |
| POST   | `/panel/api/delete-order.php` | Delete order     |
| POST   | `/panel/api/settings.php` | Save settings       |
