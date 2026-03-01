<?php
require __DIR__ . '/../partials/bootstrap.php';

panel_api_guard();
panel_method(['POST']);
panel_csrf_guard();

header('Content-Type: application/json');

$brand = trim($_POST['brand'] ?? '');
$notif = isset($_POST['notifications']) ? (bool) $_POST['notifications'] : false;

if ($brand === '') {
    http_response_code(422);
    echo json_encode(['message' => 'Brand name is required']);
    exit;
}

if (strlen($brand) > 100) {
    http_response_code(422);
    echo json_encode(['message' => 'Brand name must be 100 characters or less']);
    exit;
}

// In production this would persist to a database.
// For now, store in session to demonstrate server-side handling.
$_SESSION['settings_brand'] = $brand;
$_SESSION['settings_notif'] = $notif;

echo json_encode(['message' => 'Settings saved successfully']);
