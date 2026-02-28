<?php
require __DIR__ . '/partials/bootstrap.php';

if (!empty($_SESSION['operator_auth'])) {
  header('Location: /panel/dashboard.php');
  exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $email = trim($_POST['email'] ?? '');
  $password = trim($_POST['password'] ?? '');

  if ($email === PANEL_EMAIL && $password === PANEL_PASSWORD) {
    $_SESSION['operator_auth'] = true;
    $_SESSION['operator_name'] = 'MNK Operator';
    header('Location: /panel/dashboard.php');
    exit;
  }
  $error = 'Invalid credentials';
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
