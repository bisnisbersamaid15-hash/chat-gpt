<?php
require __DIR__ . '/partials/bootstrap.php';
panel_guard();
$title = 'Products';
$currentPage = 'products';
require __DIR__ . '/partials/layout-top.php';

$products = panel_db()->query('SELECT * FROM products ORDER BY id ASC')->fetchAll();
?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <span></span>
  <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#addProductModal">Add Product</button>
</div>
<div class="row g-3">
  <?php foreach ($products as $product): ?>
    <div class="col-md-4">
      <div class="card operator-card p-3 h-100">
        <div class="d-flex justify-content-between mb-3">
          <span class="badge text-bg-secondary"><?= htmlspecialchars($product['id']) ?></span>
          <span class="text-gold fw-semibold"><?= panel_currency((int)$product['price']) ?></span>
        </div>
        <h6><?= htmlspecialchars($product['name']) ?></h6>
        <small class="text-secondary d-block mb-2"><?= htmlspecialchars($product['provider']) ?></small>
        <div class="progress mb-3" role="progressbar">
          <div class="progress-bar bg-warning" style="width: <?= min(100, (int)$product['stock']) ?>%"></div>
        </div>
        <div class="d-flex justify-content-between align-items-center">
          <small>Stock: <?= (int)$product['stock'] ?></small>
          <div>
            <button class="btn btn-sm btn-outline-warning btn-edit-product"
              data-id="<?= htmlspecialchars($product['id']) ?>"
              data-name="<?= htmlspecialchars($product['name']) ?>"
              data-provider="<?= htmlspecialchars($product['provider']) ?>"
              data-stock="<?= (int)$product['stock'] ?>"
              data-price="<?= (int)$product['price'] ?>">Edit</button>
            <button class="btn btn-sm btn-outline-danger btn-delete-product"
              data-id="<?= htmlspecialchars($product['id']) ?>"
              data-name="<?= htmlspecialchars($product['name']) ?>">Del</button>
          </div>
        </div>
      </div>
    </div>
  <?php endforeach; ?>
</div>

<!-- Add Product Modal -->
<div class="modal fade" id="addProductModal" tabindex="-1">
  <div class="modal-dialog"><div class="modal-content bg-dark text-light border-warning">
    <div class="modal-header"><h5 class="modal-title">Add Product</h5><button class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div>
    <div class="modal-body">
      <form id="addProductForm">
        <input class="form-control mb-2" name="id" placeholder="Product ID (e.g. PRD-123)" required>
        <input class="form-control mb-2" name="name" placeholder="Product Name" required>
        <input class="form-control mb-2" name="provider" placeholder="Provider" required>
        <input class="form-control mb-2" name="stock" type="number" min="0" placeholder="Stock" required>
        <input class="form-control mb-2" name="price" type="number" min="0" placeholder="Price (Rp)" required>
      </form>
      <div id="productFeedback" class="small text-gold mt-2"></div>
    </div>
    <div class="modal-footer"><button class="btn btn-warning" id="saveProduct">Save</button></div>
  </div></div>
</div>

<!-- Edit Product Modal -->
<div class="modal fade" id="editProductModal" tabindex="-1">
  <div class="modal-dialog"><div class="modal-content bg-dark text-light border-warning">
    <div class="modal-header"><h5 class="modal-title">Edit Product</h5><button class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div>
    <div class="modal-body">
      <form id="editProductForm">
        <input type="hidden" name="original_id" id="editProductOriginalId">
        <input class="form-control mb-2" name="id" id="editProductId" placeholder="Product ID" required>
        <input class="form-control mb-2" name="name" id="editProductName" placeholder="Product Name" required>
        <input class="form-control mb-2" name="provider" id="editProductProvider" placeholder="Provider" required>
        <input class="form-control mb-2" name="stock" id="editProductStock" type="number" min="0" placeholder="Stock" required>
        <input class="form-control mb-2" name="price" id="editProductPrice" type="number" min="0" placeholder="Price (Rp)" required>
      </form>
      <div id="editProductFeedback" class="small text-gold mt-2"></div>
    </div>
    <div class="modal-footer"><button class="btn btn-warning" id="updateProduct">Update</button></div>
  </div></div>
</div>

<?php require __DIR__ . '/partials/layout-bottom.php'; ?>
