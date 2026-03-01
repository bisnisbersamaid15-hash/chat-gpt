<?php
require __DIR__ . '/../partials/bootstrap.php';

panel_api_guard();
panel_method(['POST']);
panel_csrf_guard();

header('Content-Type: application/json');

$id       = trim($_POST['id'] ?? '');
$name     = trim($_POST['name'] ?? '');
$provider = trim($_POST['provider'] ?? '');
$stock    = (int) ($_POST['stock'] ?? 0);
$price    = (int) ($_POST['price'] ?? 0);

if ($id === '' || $name === '' || $provider === '') {
    http_response_code(422);
    echo json_encode(['message' => 'Product ID, name, and provider are required']);
    exit;
}

if ($stock < 0 || $price < 0) {
    http_response_code(422);
    echo json_encode(['message' => 'Stock and price must be non-negative']);
    exit;
}

// Check duplicate ID
$stmt = panel_db()->prepare('SELECT id FROM products WHERE id = ?');
$stmt->execute([$id]);
if ($stmt->fetch()) {
    http_response_code(422);
    echo json_encode(['message' => 'Product ID already exists']);
    exit;
}

$stmt = panel_db()->prepare('INSERT INTO products (id, name, provider, stock, price) VALUES (?, ?, ?, ?, ?)');
$stmt->execute([$id, $name, $provider, $stock, $price]);

panel_audit('create_product', 'products', "Created product {$id}: {$name}");

echo json_encode(['message' => "Product {$name} created successfully"]);
