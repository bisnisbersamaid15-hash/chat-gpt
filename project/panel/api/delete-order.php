<?php
require __DIR__ . '/../partials/bootstrap.php';

panel_api_guard();
panel_method(['POST']);
panel_csrf_guard();

header('Content-Type: application/json');

$id = trim($_POST['id'] ?? '');

if ($id === '') {
    http_response_code(422);
    echo json_encode(['message' => 'Order ID is required']);
    exit;
}

$stmt = panel_db()->prepare('SELECT customer FROM orders WHERE id = ?');
$stmt->execute([$id]);
$order = $stmt->fetch();

if (!$order) {
    http_response_code(404);
    echo json_encode(['message' => 'Order not found']);
    exit;
}

$stmt = panel_db()->prepare('DELETE FROM orders WHERE id = ?');
$stmt->execute([$id]);

panel_audit('delete_order', 'orders', "Deleted order {$id}: {$order['customer']}");

echo json_encode(['message' => "Order {$id} deleted successfully"]);
