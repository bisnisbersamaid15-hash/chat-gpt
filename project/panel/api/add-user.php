<?php
require __DIR__ . '/../partials/bootstrap.php';

panel_api_guard();
panel_method(['POST']);
panel_csrf_guard();

header('Content-Type: application/json');

$name  = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$role  = trim($_POST['role'] ?? 'Operator');

// Validate required fields
if ($name === '' || $email === '') {
    http_response_code(422);
    echo json_encode(['message' => 'Name and email are required']);
    exit;
}

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(422);
    echo json_encode(['message' => 'Invalid email format']);
    exit;
}

// Validate role against allowed values
$allowedRoles = ['Operator', 'Finance', 'Support', 'Admin'];
if (!in_array($role, $allowedRoles, true)) {
    http_response_code(422);
    echo json_encode(['message' => 'Invalid role']);
    exit;
}

echo json_encode(['message' => "User {$name} ({$role}) queued for approval"]);
