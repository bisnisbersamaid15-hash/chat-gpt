<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

$seed = require __DIR__ . '/../data/seed.php';

const PANEL_EMAIL = 'mnk@gmail.com';
const PANEL_PASSWORD = 'aaa123';

function panel_guard(): void
{
  if (empty($_SESSION['operator_auth'])) {
    header('Location: /panel/index.php');
    exit;
  }
}

function panel_currency(int $value): string
{
  return 'Rp ' . number_format($value, 0, ',', '.');
}

function panel_badge(string $status): string
{
  return match ($status) {
    'Paid', 'Active' => 'success',
    'Pending' => 'warning',
    'Failed', 'Inactive' => 'danger',
    default => 'secondary',
  };
}
