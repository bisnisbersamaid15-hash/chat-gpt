<?php
require __DIR__ . '/../partials/bootstrap.php';

panel_api_guard();
panel_method(['POST']);
panel_csrf_guard();

header('Content-Type: application/json');

$id = (int) ($_POST['id'] ?? 0);

if ($id <= 0) {
    http_response_code(422);
    echo json_encode(['message' => 'Valid user ID is required']);
    exit;
}

// Get user name for audit log before deleting
$stmt = panel_db()->prepare('SELECT name FROM users WHERE id = ?');
$stmt->execute([$id]);
$user = $stmt->fetch();

if (!$user) {
    http_response_code(404);
    echo json_encode(['message' => 'User not found']);
    exit;
}

$stmt = panel_db()->prepare('DELETE FROM users WHERE id = ?');
$stmt->execute([$id]);

panel_audit('delete_user', 'users', "Deleted user #{$id}: {$user['name']}");

echo json_encode(['message' => "User {$user['name']} deleted successfully"]);
