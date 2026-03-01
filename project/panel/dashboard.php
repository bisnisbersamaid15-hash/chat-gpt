<?php
require __DIR__ . '/partials/bootstrap.php';
panel_guard();
$title = 'Dashboard';
$currentPage = 'dashboard';
require __DIR__ . '/partials/layout-top.php';

$db = panel_db();

// Real-time KPI from database
$revenueToday = $db->query("SELECT COALESCE(SUM(amount), 0) FROM orders WHERE status = 'Paid' AND date = date('now')")->fetchColumn();
$revenueTotalPaid = $db->query("SELECT COALESCE(SUM(amount), 0) FROM orders WHERE status = 'Paid'")->fetchColumn();
$activeUsers = $db->query("SELECT COUNT(*) FROM users WHERE status = 'Active'")->fetchColumn();
$pendingOrders = $db->query("SELECT COUNT(*) FROM orders WHERE status = 'Pending'")->fetchColumn();
$totalOrders = $db->query("SELECT COUNT(*) FROM orders")->fetchColumn();

// Latest orders
$orders = $db->query("SELECT * FROM orders ORDER BY date DESC, id DESC LIMIT 10")->fetchAll();
?>
<div class="row g-3 mb-4">
  <div class="col-md-3"><div class="card operator-card p-3"><small>Revenue (Paid Total)</small><h5 class="text-gold mt-2"><?= panel_currency((int)$revenueTotalPaid) ?></h5></div></div>
  <div class="col-md-3"><div class="card operator-card p-3"><small>Active Users</small><h5 class="text-gold mt-2"><?= number_format((int)$activeUsers) ?></h5></div></div>
  <div class="col-md-3"><div class="card operator-card p-3"><small>Pending Orders</small><h5 class="text-gold mt-2"><?= (int)$pendingOrders ?></h5></div></div>
  <div class="col-md-3"><div class="card operator-card p-3"><small>Total Orders</small><h5 class="text-gold mt-2"><?= (int)$totalOrders ?></h5></div></div>
</div>
<div class="card operator-card p-3 mb-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h6 class="mb-0">Latest Orders</h6>
    <button class="btn btn-sm btn-outline-warning" id="refreshOrders">Refresh</button>
  </div>
  <div class="table-responsive">
    <table class="table table-dark align-middle mb-0" id="ordersPreview">
      <thead><tr><th>Invoice</th><th>Customer</th><th>Amount</th><th>Status</th></tr></thead>
      <tbody>
      <?php foreach ($orders as $order): ?>
        <tr>
          <td><?= htmlspecialchars($order['id']) ?></td>
          <td><?= htmlspecialchars($order['customer']) ?></td>
          <td><?= panel_currency((int)$order['amount']) ?></td>
          <td><span class="badge text-bg-<?= panel_badge($order['status']) ?>"><?= htmlspecialchars($order['status']) ?></span></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
<?php require __DIR__ . '/partials/layout-bottom.php'; ?>
