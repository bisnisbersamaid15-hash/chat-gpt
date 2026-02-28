<?php
require __DIR__ . '/partials/bootstrap.php';
panel_guard();
$title = 'Dashboard';
$currentPage = 'dashboard';
require __DIR__ . '/partials/layout-top.php';
$stats = $seed['stats'];
?>
<div class="row g-3 mb-4">
  <div class="col-md-3"><div class="card operator-card p-3"><small>Revenue Today</small><h5 class="text-gold mt-2"><?= panel_currency($stats['revenue_today']) ?></h5></div></div>
  <div class="col-md-3"><div class="card operator-card p-3"><small>Active Users</small><h5 class="text-gold mt-2"><?= number_format($stats['active_users']) ?></h5></div></div>
  <div class="col-md-3"><div class="card operator-card p-3"><small>Pending Orders</small><h5 class="text-gold mt-2"><?= $stats['pending_orders'] ?></h5></div></div>
  <div class="col-md-3"><div class="card operator-card p-3"><small>Tickets</small><h5 class="text-gold mt-2"><?= $stats['tickets'] ?></h5></div></div>
</div>
<div class="card operator-card p-3 mb-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h6 class="mb-0">Latest Orders</h6>
    <button class="btn btn-sm btn-outline-warning" id="refreshOrders">Refresh via jQuery</button>
  </div>
  <div class="table-responsive">
    <table class="table table-dark align-middle mb-0" id="ordersPreview">
      <thead><tr><th>Invoice</th><th>Customer</th><th>Amount</th><th>Status</th></tr></thead>
      <tbody>
      <?php foreach ($seed['orders'] as $order): ?>
        <tr>
          <td><?= $order['id'] ?></td>
          <td><?= htmlspecialchars($order['customer']) ?></td>
          <td><?= panel_currency($order['amount']) ?></td>
          <td><span class="badge text-bg-<?= panel_badge($order['status']) ?>"><?= $order['status'] ?></span></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
<?php require __DIR__ . '/partials/layout-bottom.php'; ?>
