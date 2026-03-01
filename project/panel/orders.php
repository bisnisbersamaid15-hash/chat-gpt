<?php
require __DIR__ . '/partials/bootstrap.php';
panel_guard();
$title = 'Orders';
$currentPage = 'orders';
require __DIR__ . '/partials/layout-top.php';

$orders = panel_db()->query('SELECT * FROM orders ORDER BY date DESC, id DESC')->fetchAll();
?>
<div class="card operator-card p-3">
  <div class="d-flex justify-content-between mb-3">
    <h6 class="mb-0">Orders List</h6>
    <div class="d-flex gap-2">
      <input id="orderFilter" class="form-control form-control-sm w-auto" placeholder="Filter customer">
      <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#addOrderModal">Add Order</button>
    </div>
  </div>
  <div class="table-responsive">
    <table class="table table-dark mb-0" id="ordersTable">
      <thead><tr><th>Invoice</th><th>Customer</th><th>Date</th><th>Amount</th><th>Status</th><th>Actions</th></tr></thead>
      <tbody>
      <?php foreach ($orders as $order): ?>
      <tr data-id="<?= htmlspecialchars($order['id']) ?>">
        <td><?= htmlspecialchars($order['id']) ?></td>
        <td class="filter-target"><?= htmlspecialchars($order['customer']) ?></td>
        <td><?= htmlspecialchars($order['date']) ?></td>
        <td><?= panel_currency((int)$order['amount']) ?></td>
        <td><span class="badge text-bg-<?= panel_badge($order['status']) ?>"><?= htmlspecialchars($order['status']) ?></span></td>
        <td>
          <button class="btn btn-sm btn-outline-warning btn-edit-order"
            data-id="<?= htmlspecialchars($order['id']) ?>"
            data-customer="<?= htmlspecialchars($order['customer']) ?>"
            data-amount="<?= (int)$order['amount'] ?>"
            data-status="<?= htmlspecialchars($order['status']) ?>"
            data-date="<?= htmlspecialchars($order['date']) ?>">Edit</button>
          <button class="btn btn-sm btn-outline-danger btn-delete-order"
            data-id="<?= htmlspecialchars($order['id']) ?>">Del</button>
        </td>
      </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Add Order Modal -->
<div class="modal fade" id="addOrderModal" tabindex="-1">
  <div class="modal-dialog"><div class="modal-content bg-dark text-light border-warning">
    <div class="modal-header"><h5 class="modal-title">Add Order</h5><button class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div>
    <div class="modal-body">
      <form id="addOrderForm">
        <input class="form-control mb-2" name="id" placeholder="Invoice ID (e.g. INV-9025)" required>
        <input class="form-control mb-2" name="customer" placeholder="Customer Name" required>
        <input class="form-control mb-2" name="amount" type="number" min="0" placeholder="Amount (Rp)" required>
        <input class="form-control mb-2" name="date" type="date" required>
        <select class="form-select mb-2" name="status"><option>Pending</option><option>Paid</option><option>Failed</option></select>
      </form>
      <div id="orderFeedback" class="small text-gold mt-2"></div>
    </div>
    <div class="modal-footer"><button class="btn btn-warning" id="saveOrder">Save</button></div>
  </div></div>
</div>

<!-- Edit Order Modal -->
<div class="modal fade" id="editOrderModal" tabindex="-1">
  <div class="modal-dialog"><div class="modal-content bg-dark text-light border-warning">
    <div class="modal-header"><h5 class="modal-title">Edit Order</h5><button class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div>
    <div class="modal-body">
      <form id="editOrderForm">
        <input type="hidden" name="original_id" id="editOrderOriginalId">
        <input class="form-control mb-2" name="id" id="editOrderId" placeholder="Invoice ID" required>
        <input class="form-control mb-2" name="customer" id="editOrderCustomer" placeholder="Customer" required>
        <input class="form-control mb-2" name="amount" id="editOrderAmount" type="number" min="0" placeholder="Amount" required>
        <input class="form-control mb-2" name="date" id="editOrderDate" type="date" required>
        <select class="form-select mb-2" name="status" id="editOrderStatus"><option>Pending</option><option>Paid</option><option>Failed</option></select>
      </form>
      <div id="editOrderFeedback" class="small text-gold mt-2"></div>
    </div>
    <div class="modal-footer"><button class="btn btn-warning" id="updateOrder">Update</button></div>
  </div></div>
</div>

<?php require __DIR__ . '/partials/layout-bottom.php'; ?>
