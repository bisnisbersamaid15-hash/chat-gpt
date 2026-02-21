<?php
$currentPage = $currentPage ?? 'dashboard';
$title = $title ?? 'Operator Panel';
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= htmlspecialchars($title) ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link href="/panel/assets/css/operator.css" rel="stylesheet">
</head>
<body>
<div class="operator-shell d-flex">
  <aside class="sidebar p-3">
    <h5 class="text-gold fw-bold mb-4">Operator Panel</h5>
    <nav class="nav flex-column gap-2">
      <?php $menus = [
        'dashboard' => ['Dashboard', 'bi-speedometer2', '/panel/dashboard.php'],
        'users' => ['Users', 'bi-people', '/panel/users.php'],
        'products' => ['Products', 'bi-grid', '/panel/products.php'],
        'orders' => ['Orders', 'bi-receipt', '/panel/orders.php'],
        'reports' => ['Reports', 'bi-bar-chart', '/panel/reports.php'],
        'settings' => ['Settings', 'bi-gear', '/panel/settings.php'],
      ];
      foreach ($menus as $key => $menu):
      ?>
      <a class="nav-link operator-link <?= $currentPage === $key ? 'active' : '' ?>" href="<?= $menu[2] ?>">
        <i class="bi <?= $menu[1] ?> me-2"></i><?= $menu[0] ?>
      </a>
      <?php endforeach; ?>
    </nav>
  </aside>
  <main class="main flex-grow-1 p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h4 class="mb-0"><?= htmlspecialchars($title) ?></h4>
      <div>
        <span class="badge text-bg-dark border border-warning me-3">Hi, <?= htmlspecialchars($_SESSION['operator_name'] ?? 'Operator') ?></span>
        <a href="/panel/logout.php" class="btn btn-sm btn-outline-warning">Logout</a>
      </div>
    </div>
