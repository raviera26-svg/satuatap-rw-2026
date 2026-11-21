<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../middleware/auth.php';

header('Content-Type: application/json');
startSession();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'message' => 'Method tidak diizinkan'], 405);
}

$data = json_decode(file_get_contents('php://input'), true);
$username = trim($data['username'] ?? '');
$password = $data['password'] ?? '';

if (!$username || !$password) {
    jsonResponse(['success' => false, 'message' => 'Username dan password wajib diisi']);
}

$pdo = getDB();
$stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? AND status = 'aktif' LIMIT 1");
$stmt->execute([$username]);
$user = $stmt->fetch();

if (!$user || !password_verify($password, $user['password'])) {
    jsonResponse(['success' => false, 'message' => 'Username atau password salah']);
}

// Update last login
$pdo->prepare("UPDATE users SET last_login = NOW() WHERE id = ?")->execute([$user['id']]);

// Set session
$_SESSION['user_id']  = $user['id'];
$_SESSION['username'] = $user['username'];
$_SESSION['nama']     = $user['nama_lengkap'];
$_SESSION['role']     = $user['role'];
$_SESSION['nomor_rt'] = $user['nomor_rt'];

$redirect = $user['role'] === 'rw' ? '/rw/dashboard.php' : '/rt/dashboard.php';

jsonResponse([
    'success'  => true,
    'role'     => $user['role'],
    'redirect' => $redirect,
]);
?>
