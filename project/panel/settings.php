<?php
require __DIR__ . '/partials/bootstrap.php';
panel_guard();
$title = 'Settings';
$currentPage = 'settings';
require __DIR__ . '/partials/layout-top.php';
?>
<div class="card operator-card p-3">
  <h6>Panel Settings</h6>
  <form id="settingsForm" class="mt-3">
    <div class="mb-3">
      <label class="form-label">Brand Name</label>
      <input class="form-control" name="brand" value="Operator Hitam Emas">
    </div>
    <div class="mb-3 form-check form-switch">
      <input class="form-check-input" type="checkbox" role="switch" id="notifSwitch" checked>
      <label class="form-check-label" for="notifSwitch">Enable notification sound</label>
    </div>
    <button class="btn btn-warning" type="submit">Save changes</button>
    <div id="settingsFeedback" class="small text-gold mt-2"></div>
  </form>
</div>
<?php require __DIR__ . '/partials/layout-bottom.php'; ?>
