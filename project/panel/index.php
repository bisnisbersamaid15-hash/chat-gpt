<?php
require __DIR__ . '/partials/bootstrap.php';

if (!empty($_SESSION['operator_auth'])) {
    header('Location: /panel/dashboard.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF token
    if (!panel_csrf_verify()) {
        $error = 'Invalid form submission. Please try again.';
    } else {
        $email    = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');
        $ip       = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';

        // Rate limiting: check if IP is locked out
        $lockout = panel_rate_check($ip);
        if ($lockout > 0) {
            $minutes = (int) ceil($lockout / 60);
            $error = "Too many failed attempts. Try again in {$minutes} minute(s).";
        } else {
            // Look up admin in database
            $stmt = panel_db()->prepare('SELECT id, name, password FROM admins WHERE email = ?');
            $stmt->execute([$email]);
            $admin = $stmt->fetch();

            if ($admin && password_verify($password, $admin['password'])) {
                // Record successful login
                panel_rate_record($email, $ip, true);

                // Regenerate session ID to prevent session fixation
                session_regenerate_id(true);
                $_SESSION['operator_auth']     = true;
                $_SESSION['operator_name']     = $admin['name'];
                $_SESSION['operator_admin_id'] = $admin['id'];
                // Refresh CSRF token after login
                unset($_SESSION['csrf_token']);

                // Audit log
                panel_audit('login', 'admins', "Admin #{$admin['id']} logged in");

                header('Location: /panel/dashboard.php');
                exit;
            }

            // Record failed attempt
            panel_rate_record($email, $ip, false);
            $error = 'Invalid credentials';
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Operator Login</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="/panel/assets/css/operator.css" rel="stylesheet">
</head>
<body class="login-page d-flex align-items-center justify-content-center">
  <form class="card operator-card p-4" method="post" style="width: 360px;">
    <?= panel_csrf_field() ?>
    <h4 class="text-gold mb-3">Backend Panel Login</h4>
    <?php if ($error): ?><div class="alert alert-danger py-2"><?= htmlspecialchars($error) ?></div><?php endif; ?>
    <div class="mb-3">
      <label class="form-label">Email</label>
      <input type="email" name="email" class="form-control" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Password</label>
      <input type="password" name="password" class="form-control" required>
    </div>
    <button class="btn btn-warning w-100" type="submit">Sign in</button>
    <small class="text-secondary mt-3 d-block">Demo access: mnk@gmail.com / aaa123</small>
  </form>
</body>
</html>
