<?php
require __DIR__ . '/../partials/bootstrap.php';

panel_api_guard();
panel_method(['POST']);
panel_csrf_guard();

header('Content-Type: application/json');

$originalId = trim($_POST['original_id'] ?? '');
$id         = trim($_POST['id'] ?? '');
$name       = trim($_POST['name'] ?? '');
$provider   = trim($_POST['provider'] ?? '');
$stock      = (int) ($_POST['stock'] ?? 0);
$price      = (int) ($_POST['price'] ?? 0);

if ($originalId === '' || $id === '' || $name === '' || $provider === '') {
    http_response_code(422);
    echo json_encode(['message' => 'All fields are required']);
    exit;
}

if ($stock < 0 || $price < 0) {
    http_response_code(422);
    echo json_encode(['message' => 'Stock and price must be non-negative']);
    exit;
}

// If ID changed, check new ID doesn't conflict
if ($id !== $originalId) {
    $stmt = panel_db()->prepare('SELECT id FROM products WHERE id = ?');
    $stmt->execute([$id]);
    if ($stmt->fetch()) {
        http_response_code(422);
        echo json_encode(['message' => 'New product ID already exists']);
        exit;
    }
}

$stmt = panel_db()->prepare('UPDATE products SET id = ?, name = ?, provider = ?, stock = ?, price = ?, updated_at = datetime("now") WHERE id = ?');
$stmt->execute([$id, $name, $provider, $stock, $price, $originalId]);

if ($stmt->rowCount() === 0) {
    http_response_code(404);
    echo json_encode(['message' => 'Product not found']);
    exit;
}

panel_audit('update_product', 'products', "Updated product {$originalId} → {$id}: {$name}");

echo json_encode(['message' => "Product {$name} updated successfully"]);
