<?php
require __DIR__ . '/../partials/bootstrap.php';

panel_api_guard();
panel_method(['GET']);

$orders = $seed['orders'];

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="orders_export_' . date('Ymd_His') . '.csv"');

$output = fopen('php://output', 'w');

// CSV header row
fputcsv($output, ['Invoice', 'Customer', 'Amount', 'Status', 'Date']);

// Data rows
foreach ($orders as $order) {
    fputcsv($output, [
        $order['id'],
        $order['customer'],
        $order['amount'],
        $order['status'],
        $order['date'],
    ]);
}

fclose($output);
