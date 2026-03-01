<?php
require __DIR__ . '/partials/bootstrap.php';
panel_guard();
$title = 'Settings';
$currentPage = 'settings';
require __DIR__ . '/partials/layout-top.php';

$brand = panel_setting('brand', 'Operator Hitam Emas');
$notif = panel_setting('notifications', '1') === '1';
?>
<div class="card operator-card p-3">
  <h6>Panel Settings</h6>
  <form id="settingsForm" class="mt-3">
    <?= panel_csrf_field() ?>
    <div class="mb-3">
      <label class="form-label">Brand Name</label>
      <input class="form-control" name="brand" value="<?= htmlspecialchars($brand) ?>" maxlength="100">
    </div>
    <div class="mb-3 form-check form-switch">
      <input class="form-check-input" type="checkbox" role="switch" id="notifSwitch" <?= $notif ? 'checked' : '' ?>>
      <label class="form-check-label" for="notifSwitch">Enable notification sound</label>
    </div>
    <button class="btn btn-warning" type="submit">Save changes</button>
    <div id="settingsFeedback" class="small text-gold mt-2"></div>
  </form>
</div>

<div class="card operator-card p-3 mt-3">
  <h6>Audit Log</h6>
  <div class="table-responsive">
    <table class="table table-dark table-sm mb-0">
      <thead><tr><th>Time</th><th>Action</th><th>Target</th><th>Detail</th><th>IP</th></tr></thead>
      <tbody>
      <?php
      $logs = panel_db()->query('SELECT * FROM audit_log ORDER BY created_at DESC LIMIT 20')->fetchAll();
      foreach ($logs as $log): ?>
      <tr>
        <td><small><?= htmlspecialchars($log['created_at']) ?></small></td>
        <td><span class="badge text-bg-secondary"><?= htmlspecialchars($log['action']) ?></span></td>
        <td><?= htmlspecialchars($log['target'] ?? '-') ?></td>
        <td><small><?= htmlspecialchars($log['detail'] ?? '-') ?></small></td>
        <td><small><?= htmlspecialchars($log['ip'] ?? '-') ?></small></td>
      </tr>
      <?php endforeach; ?>
      <?php if (empty($logs)): ?><tr><td colspan="5" class="text-secondary text-center">No audit entries yet</td></tr><?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<?php require __DIR__ . '/partials/layout-bottom.php'; ?>
