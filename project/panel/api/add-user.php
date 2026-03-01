<?php
require __DIR__ . '/../partials/bootstrap.php';

panel_api_guard();
panel_method(['POST']);
panel_csrf_guard();

header('Content-Type: application/json');

$name  = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$role  = trim($_POST['role'] ?? 'Operator');

if ($name === '' || $email === '') {
    http_response_code(422);
    echo json_encode(['message' => 'Name and email are required']);
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

// Check for duplicate email
$stmt = panel_db()->prepare('SELECT id FROM users WHERE email = ?');
$stmt->execute([$email]);
if ($stmt->fetch()) {
    http_response_code(422);
    echo json_encode(['message' => 'Email already exists']);
    exit;
}

$stmt = panel_db()->prepare('INSERT INTO users (name, email, role) VALUES (?, ?, ?)');
$stmt->execute([$name, $email, $role]);
$newId = panel_db()->lastInsertId();

panel_audit('create_user', 'users', "Created user #{$newId}: {$name} ({$role})");

echo json_encode(['message' => "User {$name} ({$role}) created successfully", 'id' => (int)$newId]);
