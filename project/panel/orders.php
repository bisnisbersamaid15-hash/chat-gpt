<?php
require __DIR__ . '/partials/bootstrap.php';
panel_guard();
$title = 'Orders';
$currentPage = 'orders';
require __DIR__ . '/partials/layout-top.php';
?>
<div class="card operator-card p-3">
  <div class="d-flex justify-content-between mb-3"><h6 class="mb-0">Orders List</h6><input id="orderFilter" class="form-control form-control-sm w-auto" placeholder="Filter customer"></div>
  <div class="table-responsive">
    <table class="table table-dark mb-0" id="ordersTable">
      <thead><tr><th>Invoice</th><th>Customer</th><th>Date</th><th>Amount</th><th>Status</th></tr></thead>
      <tbody>
      <?php foreach ($seed['orders'] as $order): ?>
      <tr>
        <td><?= $order['id'] ?></td>
        <td class="filter-target"><?= htmlspecialchars($order['customer']) ?></td>
        <td><?= $order['date'] ?></td>
        <td><?= panel_currency($order['amount']) ?></td>
        <td><span class="badge text-bg-<?= panel_badge($order['status']) ?>"><?= $order['status'] ?></span></td>
      </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
<?php require __DIR__ . '/partials/layout-bottom.php'; ?>
