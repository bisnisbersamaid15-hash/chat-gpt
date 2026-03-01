<?php
require __DIR__ . '/../partials/bootstrap.php';

panel_api_guard();
panel_method(['POST']);
panel_csrf_guard();

header('Content-Type: application/json');

$id     = (int) ($_POST['id'] ?? 0);
$name   = trim($_POST['name'] ?? '');
$email  = trim($_POST['email'] ?? '');
$role   = trim($_POST['role'] ?? '');
$status = trim($_POST['status'] ?? '');

if ($id <= 0 || $name === '' || $email === '') {
    http_response_code(422);
    echo json_encode(['message' => 'ID, name, and email are required']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(422);
    echo json_encode(['message' => 'Invalid email format']);
    exit;
}

$allowedRoles = ['Operator', 'Finance', 'Support', 'Admin'];
if (!in_array($role, $allowedRoles, true)) {
    http_response_code(422);
    echo json_encode(['message' => 'Invalid role']);
    exit;
}

$allowedStatuses = ['Active', 'Inactive'];
if (!in_array($status, $allowedStatuses, true)) {
    http_response_code(422);
    echo json_encode(['message' => 'Invalid status']);
    exit;
}

// Check email uniqueness (exclude current user)
$stmt = panel_db()->prepare('SELECT id FROM users WHERE email = ? AND id != ?');
$stmt->execute([$email, $id]);
if ($stmt->fetch()) {
    http_response_code(422);
    echo json_encode(['message' => 'Email already in use by another user']);
    exit;
}

$stmt = panel_db()->prepare('UPDATE users SET name = ?, email = ?, role = ?, status = ?, updated_at = datetime("now") WHERE id = ?');
$stmt->execute([$name, $email, $role, $status, $id]);

if ($stmt->rowCount() === 0) {
    http_response_code(404);
    echo json_encode(['message' => 'User not found']);
    exit;
}

panel_audit('update_user', 'users', "Updated user #{$id}: {$name}");

echo json_encode(['message' => "User {$name} updated successfully"]);
