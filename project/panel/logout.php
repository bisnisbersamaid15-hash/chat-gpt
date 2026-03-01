<?php
require __DIR__ . '/partials/bootstrap.php';

// Audit log before destroying session
if (!empty($_SESSION['operator_admin_id'])) {
    panel_audit('logout', 'admins', "Admin #{$_SESSION['operator_admin_id']} logged out");
}

$_SESSION = [];

if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params['path'], $params['domain'],
        $params['secure'], $params['httponly']
    );
}

session_destroy();
header('Location: /panel/index.php');
exit;
