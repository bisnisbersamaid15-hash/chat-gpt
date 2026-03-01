<?php
require __DIR__ . '/../partials/bootstrap.php';

panel_api_guard();
panel_method(['POST']);
panel_csrf_guard();

header('Content-Type: application/json');

$originalId = trim($_POST['original_id'] ?? '');
$id         = trim($_POST['id'] ?? '');
$customer   = trim($_POST['customer'] ?? '');
$amount     = (int) ($_POST['amount'] ?? 0);
$date       = trim($_POST['date'] ?? '');
$status     = trim($_POST['status'] ?? '');

if ($originalId === '' || $id === '' || $customer === '' || $date === '') {
    http_response_code(422);
    echo json_encode(['message' => 'All fields are required']);
    exit;
}

$allowedStatuses = ['Pending', 'Paid', 'Failed'];
if (!in_array($status, $allowedStatuses, true)) {
    http_response_code(422);
    echo json_encode(['message' => 'Invalid status']);
    exit;
}

if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
    http_response_code(422);
    echo json_encode(['message' => 'Invalid date format']);
    exit;
}

// If ID changed, check new ID doesn't conflict
if ($id !== $originalId) {
    $stmt = panel_db()->prepare('SELECT id FROM orders WHERE id = ?');
    $stmt->execute([$id]);
    if ($stmt->fetch()) {
        http_response_code(422);
        echo json_encode(['message' => 'New invoice ID already exists']);
        exit;
    }
}

$stmt = panel_db()->prepare('UPDATE orders SET id = ?, customer = ?, amount = ?, status = ?, date = ? WHERE id = ?');
$stmt->execute([$id, $customer, $amount, $status, $date, $originalId]);

if ($stmt->rowCount() === 0) {
    http_response_code(404);
    echo json_encode(['message' => 'Order not found']);
    exit;
}

panel_audit('update_order', 'orders', "Updated order {$originalId}: status={$status}");

echo json_encode(['message' => "Order {$id} updated successfully"]);
