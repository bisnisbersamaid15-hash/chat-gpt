<?php
require __DIR__ . '/partials/bootstrap.php';
panel_guard();
$title = 'Reports';
$currentPage = 'reports';
require __DIR__ . '/partials/layout-top.php';

$db = panel_db();
$paid    = (int) $db->query("SELECT COALESCE(SUM(amount), 0) FROM orders WHERE status = 'Paid'")->fetchColumn();
$pending = (int) $db->query("SELECT COALESCE(SUM(amount), 0) FROM orders WHERE status = 'Pending'")->fetchColumn();
$failed  = (int) $db->query("SELECT COALESCE(SUM(amount), 0) FROM orders WHERE status = 'Failed'")->fetchColumn();
?>
<div class="row g-3">
  <div class="col-md-4"><div class="card operator-card p-3"><small>Paid Volume</small><h5 class="text-success mt-2"><?= panel_currency($paid) ?></h5></div></div>
  <div class="col-md-4"><div class="card operator-card p-3"><small>Pending Volume</small><h5 class="text-warning mt-2"><?= panel_currency($pending) ?></h5></div></div>
  <div class="col-md-4"><div class="card operator-card p-3"><small>Failed Volume</small><h5 class="text-danger mt-2"><?= panel_currency($failed) ?></h5></div></div>
</div>
<div class="card operator-card p-3 mt-3">
  <h6>Export</h6>
  <p class="text-secondary">Download all orders as CSV for review submission.</p>
  <button class="btn btn-outline-warning btn-sm" id="simulateExport">Generate CSV</button>
  <div id="exportFeedback" class="small text-gold mt-2"></div>
</div>
<?php require __DIR__ . '/partials/layout-bottom.php'; ?>
