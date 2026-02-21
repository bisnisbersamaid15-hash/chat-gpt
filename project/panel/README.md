# Backend Panel (SSR PHP + Bootstrap + jQuery)

## Structure

- `index.php` - login page
- `dashboard.php` - metrics and latest orders
- `users.php` - user list + add-user modal
- `products.php` - product cards
- `orders.php` - order table with live filter
- `reports.php` - report summary + export simulation
- `settings.php` - configurable settings form
- `api/` - jQuery AJAX endpoints
- `assets/css/operator.css` - black & gold operator theme
- `assets/js/panel.js` - interactions

## Run locally

```bash
cd project
php -S 0.0.0.0:8080
```

Then open `http://localhost:8080/panel/index.php`.

Demo credentials:
- email: `mnk@gmail.com`
- password: `aaa123`
