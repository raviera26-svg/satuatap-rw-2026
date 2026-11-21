<?php
function startSession() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

function isLoggedIn() {
    startSession();
    return isset($_SESSION['user_id']) && isset($_SESSION['role']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: /login.php?err=not_logged_in');
        exit;
    }
}

function requireRW() {
    requireLogin();
    if (($_SESSION['role'] ?? null) !== 'rw') {
        $role = $_SESSION['role'] ?? 'unknown';
        header('Location: /rt/dashboard.php?err=wrong_role&role=' . urlencode($role));
        exit;
    }
}

function requireRT() {
    requireLogin();
    if (($_SESSION['role'] ?? null) !== 'rt') {
        $role = $_SESSION['role'] ?? 'unknown';
        header('Location: /rw/dashboard.php?err=wrong_role&role=' . urlencode($role));
        exit;
    }
}


function getCurrentUser() {
    startSession();
    return [
        'id'          => $_SESSION['user_id'] ?? null,
        'username'    => $_SESSION['username'] ?? null,
        'nama'        => $_SESSION['nama'] ?? null,
        'role'        => $_SESSION['role'] ?? null,
        'nomor_rt'    => $_SESSION['nomor_rt'] ?? null,
    ];
}

function jsonResponse($data, $code = 200) {
    http_response_code($code);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}
?>
