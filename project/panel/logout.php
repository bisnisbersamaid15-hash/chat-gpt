<?php
require __DIR__ . '/partials/bootstrap.php';
session_destroy();
header('Location: /panel/index.php');
exit;
