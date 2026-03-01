<?php
require __DIR__ . '/../partials/bootstrap.php';

panel_api_guard();
panel_method(['GET']);

$orders = panel_db()->query('SELECT * FROM orders ORDER BY date DESC, id DESC')->fetchAll();

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="orders_export_' . date('Ymd_His') . '.csv"');

$output = fopen('php://output', 'w');

fputcsv($output, ['Invoice', 'Customer', 'Amount', 'Status', 'Date']);

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

panel_audit('export_csv', 'orders', 'Exported ' . count($orders) . ' orders to CSV');
