<?php
require __DIR__ . '/../partials/bootstrap.php';

panel_api_guard();
panel_method(['GET']);

header('Content-Type: application/json');
echo json_encode($seed['orders']);
