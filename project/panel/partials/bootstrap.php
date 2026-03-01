<?php
// Hide PHP version from response headers
header_remove('X-Powered-By');

// Secure session configuration
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'httponly'  => true,
        'samesite'  => 'Strict',
    ]);
    session_start();
}

$seed = require __DIR__ . '/../data/seed.php';

const PANEL_EMAIL    = 'mnk@gmail.com';
const PANEL_PASSWORD = 'aaa123';

/**
 * Redirect unauthenticated users to login page.
 */
function panel_guard(): void
{
    if (empty($_SESSION['operator_auth'])) {
        header('Location: /panel/index.php');
        exit;
    }
}

/**
 * Redirect unauthenticated API requests with a 401 JSON response.
 */
function panel_api_guard(): void
{
    if (empty($_SESSION['operator_auth'])) {
        http_response_code(401);
        header('Content-Type: application/json');
        echo json_encode(['message' => 'Unauthorized']);
        exit;
    }
}

/**
 * Enforce allowed HTTP methods. Returns 405 for disallowed methods.
 *
 * @param string[] $allowed e.g. ['GET'] or ['POST']
 */
function panel_method(array $allowed): void
{
    if (!in_array($_SERVER['REQUEST_METHOD'], $allowed, true)) {
        http_response_code(405);
        header('Content-Type: application/json');
        header('Allow: ' . implode(', ', $allowed));
        echo json_encode(['message' => 'Method not allowed']);
        exit;
    }
}

/**
 * Generate a CSRF token and store it in the session.
 */
function panel_csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Render a hidden CSRF input field for forms.
 */
function panel_csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(panel_csrf_token()) . '">';
}

/**
 * Validate the CSRF token from the request.
 * Checks both POST body and X-CSRF-Token header (for AJAX).
 */
function panel_csrf_verify(): bool
{
    $token = $_POST['csrf_token']
        ?? $_SERVER['HTTP_X_CSRF_TOKEN']
        ?? '';

    if (empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
        return false;
    }
    return true;
}

/**
 * Reject request with 403 if CSRF token is invalid.
 */
function panel_csrf_guard(): void
{
    if (!panel_csrf_verify()) {
        http_response_code(403);
        header('Content-Type: application/json');
        echo json_encode(['message' => 'Invalid or missing CSRF token']);
        exit;
    }
}

/**
 * Format an integer as Indonesian Rupiah.
 */
function panel_currency(int $value): string
{
    return 'Rp ' . number_format($value, 0, ',', '.');
}

/**
 * Map a status string to a Bootstrap badge color class.
 */
function panel_badge(string $status): string
{
    return match ($status) {
        'Paid', 'Active'       => 'success',
        'Pending'              => 'warning',
        'Failed', 'Inactive'   => 'danger',
        default                => 'secondary',
    };
}
