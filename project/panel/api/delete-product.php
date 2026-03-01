<?php
require __DIR__ . '/../partials/bootstrap.php';

panel_api_guard();
panel_method(['POST']);
panel_csrf_guard();

header('Content-Type: application/json');

$id = trim($_POST['id'] ?? '');

if ($id === '') {
    http_response_code(422);
    echo json_encode(['message' => 'Product ID is required']);
    exit;
}

$stmt = panel_db()->prepare('SELECT name FROM products WHERE id = ?');
$stmt->execute([$id]);
$product = $stmt->fetch();

if (!$product) {
    http_response_code(404);
    echo json_encode(['message' => 'Product not found']);
    exit;
}

$stmt = panel_db()->prepare('DELETE FROM products WHERE id = ?');
$stmt->execute([$id]);

panel_audit('delete_product', 'products', "Deleted product {$id}: {$product['name']}");

echo json_encode(['message' => "Product {$product['name']} deleted successfully"]);
