<?php
require __DIR__ . '/../partials/bootstrap.php';

panel_api_guard();
panel_method(['POST']);
panel_csrf_guard();

header('Content-Type: application/json');

$id       = trim($_POST['id'] ?? '');
$customer = trim($_POST['customer'] ?? '');
$amount   = (int) ($_POST['amount'] ?? 0);
$date     = trim($_POST['date'] ?? '');
$status   = trim($_POST['status'] ?? 'Pending');

if ($id === '' || $customer === '' || $date === '') {
    http_response_code(422);
    echo json_encode(['message' => 'Invoice ID, customer, and date are required']);
    exit;
}

if ($amount < 0) {
    http_response_code(422);
    echo json_encode(['message' => 'Amount must be non-negative']);
    exit;
}

$allowedStatuses = ['Pending', 'Paid', 'Failed'];
if (!in_array($status, $allowedStatuses, true)) {
    http_response_code(422);
    echo json_encode(['message' => 'Invalid status']);
    exit;
}

// Validate date format
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
    http_response_code(422);
    echo json_encode(['message' => 'Invalid date format (YYYY-MM-DD)']);
    exit;
}

// Check duplicate ID
$stmt = panel_db()->prepare('SELECT id FROM orders WHERE id = ?');
$stmt->execute([$id]);
if ($stmt->fetch()) {
    http_response_code(422);
    echo json_encode(['message' => 'Invoice ID already exists']);
    exit;
}

$stmt = panel_db()->prepare('INSERT INTO orders (id, customer, amount, status, date) VALUES (?, ?, ?, ?, ?)');
$stmt->execute([$id, $customer, $amount, $status, $date]);

panel_audit('create_order', 'orders', "Created order {$id}: {$customer} Rp{$amount}");

echo json_encode(['message' => "Order {$id} created successfully"]);
