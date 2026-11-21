<?php
require_once __DIR__ . '/../middleware/auth.php';
startSession();
session_destroy();
header('Location: /login.php');
exit;
?>
