<?php
require __DIR__ . '/../partials/bootstrap.php';
header('Content-Type: application/json');
$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$role = trim($_POST['role'] ?? 'Operator');
if ($name === '' || $email === '') {
  http_response_code(422);
  echo json_encode(['message' => 'Name and email are required']);
  exit;
}
echo json_encode(['message' => "User {$name} ({$role}) queued for approval"]);
