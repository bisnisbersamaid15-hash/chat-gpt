<?php
require __DIR__ . '/../partials/bootstrap.php';
header('Content-Type: application/json');
echo json_encode($seed['orders']);
