<?php
require __DIR__ . '/partials/bootstrap.php';
panel_guard();
$title = 'Users';
$currentPage = 'users';
require __DIR__ . '/partials/layout-top.php';

$users = panel_db()->query('SELECT * FROM users ORDER BY id ASC')->fetchAll();
?>
<div class="card operator-card p-3">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h6 class="mb-0">User Management</h6>
    <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#addUserModal">Add User</button>
  </div>
  <div class="table-responsive">
    <table class="table table-dark mb-0" id="usersTable">
      <thead><tr><th>ID</th><th>Name</th><th>Email</th><th>Role</th><th>Status</th><th>Actions</th></tr></thead>
      <tbody>
      <?php foreach ($users as $user): ?>
      <tr data-id="<?= $user['id'] ?>">
        <td><?= $user['id'] ?></td>
        <td><?= htmlspecialchars($user['name']) ?></td>
        <td><?= htmlspecialchars($user['email']) ?></td>
        <td><?= htmlspecialchars($user['role']) ?></td>
        <td><span class="badge text-bg-<?= panel_badge($user['status']) ?>"><?= htmlspecialchars($user['status']) ?></span></td>
        <td>
          <button class="btn btn-sm btn-outline-warning btn-edit-user" data-id="<?= $user['id'] ?>" data-name="<?= htmlspecialchars($user['name']) ?>" data-email="<?= htmlspecialchars($user['email']) ?>" data-role="<?= htmlspecialchars($user['role']) ?>" data-status="<?= htmlspecialchars($user['status']) ?>">Edit</button>
          <button class="btn btn-sm btn-outline-danger btn-delete-user" data-id="<?= $user['id'] ?>" data-name="<?= htmlspecialchars($user['name']) ?>">Delete</button>
        </td>
      </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1">
  <div class="modal-dialog"><div class="modal-content bg-dark text-light border-warning">
    <div class="modal-header"><h5 class="modal-title">Add User</h5><button class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div>
    <div class="modal-body">
      <form id="addUserForm">
        <input class="form-control mb-2" name="name" placeholder="Name" required>
        <input class="form-control mb-2" name="email" type="email" placeholder="Email" required>
        <select class="form-select mb-2" name="role"><option>Operator</option><option>Finance</option><option>Support</option><option>Admin</option></select>
      </form>
      <div id="userFeedback" class="small text-gold mt-2"></div>
    </div>
    <div class="modal-footer"><button class="btn btn-warning" id="saveUser">Save</button></div>
  </div></div>
</div>

<!-- Edit User Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1">
  <div class="modal-dialog"><div class="modal-content bg-dark text-light border-warning">
    <div class="modal-header"><h5 class="modal-title">Edit User</h5><button class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div>
    <div class="modal-body">
      <form id="editUserForm">
        <input type="hidden" name="id" id="editUserId">
        <input class="form-control mb-2" name="name" id="editUserName" placeholder="Name" required>
        <input class="form-control mb-2" name="email" id="editUserEmail" type="email" placeholder="Email" required>
        <select class="form-select mb-2" name="role" id="editUserRole"><option>Operator</option><option>Finance</option><option>Support</option><option>Admin</option></select>
        <select class="form-select mb-2" name="status" id="editUserStatus"><option>Active</option><option>Inactive</option></select>
      </form>
      <div id="editUserFeedback" class="small text-gold mt-2"></div>
    </div>
    <div class="modal-footer"><button class="btn btn-warning" id="updateUser">Update</button></div>
  </div></div>
</div>

<?php require __DIR__ . '/partials/layout-bottom.php'; ?>
