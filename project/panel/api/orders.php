<?php
require __DIR__ . '/../partials/bootstrap.php';

panel_api_guard();
panel_method(['GET']);

header('Content-Type: application/json');

$orders = panel_db()->query('SELECT * FROM orders ORDER BY date DESC, id DESC')->fetchAll();
echo json_encode($orders);
