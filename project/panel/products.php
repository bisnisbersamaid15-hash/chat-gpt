<?php
require __DIR__ . '/partials/bootstrap.php';
panel_guard();
$title = 'Products';
$currentPage = 'products';
require __DIR__ . '/partials/layout-top.php';
?>
<div class="row g-3">
  <?php foreach ($seed['products'] as $product): ?>
    <div class="col-md-4">
      <div class="card operator-card p-3 h-100">
        <div class="d-flex justify-content-between mb-3">
          <span class="badge text-bg-secondary"><?= $product['id'] ?></span>
          <span class="text-gold fw-semibold"><?= panel_currency($product['price']) ?></span>
        </div>
        <h6><?= htmlspecialchars($product['name']) ?></h6>
        <small class="text-secondary d-block mb-2"><?= htmlspecialchars($product['provider']) ?></small>
        <div class="progress mb-3" role="progressbar">
          <div class="progress-bar bg-warning" style="width: <?= min(100, $product['stock']) ?>%"></div>
        </div>
        <small>Stock: <?= $product['stock'] ?></small>
      </div>
    </div>
  <?php endforeach; ?>
</div>
<?php require __DIR__ . '/partials/layout-bottom.php'; ?>
