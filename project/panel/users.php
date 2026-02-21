<?php
require __DIR__ . '/partials/bootstrap.php';
panel_guard();
$title = 'Users';
$currentPage = 'users';
require __DIR__ . '/partials/layout-top.php';
?>
<div class="card operator-card p-3">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h6 class="mb-0">User Management</h6>
    <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#addUserModal">Add User</button>
  </div>
  <div class="table-responsive">
    <table class="table table-dark mb-0">
      <thead><tr><th>ID</th><th>Name</th><th>Email</th><th>Role</th><th>Status</th></tr></thead>
      <tbody>
      <?php foreach ($seed['users'] as $user): ?>
      <tr>
        <td><?= $user['id'] ?></td><td><?= htmlspecialchars($user['name']) ?></td><td><?= htmlspecialchars($user['email']) ?></td><td><?= $user['role'] ?></td>
        <td><span class="badge text-bg-<?= panel_badge($user['status']) ?>"><?= $user['status'] ?></span></td>
      </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<div class="modal fade" id="addUserModal" tabindex="-1">
  <div class="modal-dialog"><div class="modal-content bg-dark text-light border-warning">
    <div class="modal-header"><h5 class="modal-title">Add User</h5><button class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div>
    <div class="modal-body">
      <form id="addUserForm">
        <input class="form-control mb-2" name="name" placeholder="Name" required>
        <input class="form-control mb-2" name="email" placeholder="Email" required>
        <select class="form-select" name="role"><option>Operator</option><option>Finance</option><option>Support</option></select>
      </form>
      <div id="userFeedback" class="small text-gold mt-2"></div>
    </div>
    <div class="modal-footer"><button class="btn btn-warning" id="saveUser">Save</button></div>
  </div></div>
</div>
<?php require __DIR__ . '/partials/layout-bottom.php'; ?>
