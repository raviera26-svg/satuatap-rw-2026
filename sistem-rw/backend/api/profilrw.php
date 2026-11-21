<?php
require_once __DIR__ . '/../../backend/config/database.php';
require_once __DIR__ . '/../../backend/middleware/auth.php';

requireLogin();
header('Content-Type: application/json');

$pdo    = getDB();
$user   = getCurrentUser();
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {

    case 'GET':
        $stmt = $pdo->prepare("SELECT id, username, nama_lengkap, role, nomor_rt, nomor_wa, email, foto, status, last_login, created_at FROM users WHERE id = ?");
        $stmt->execute([$user['id']]);
        $data = $stmt->fetch();
        echo json_encode(['success' => true, 'data' => $data]);
        break;

    case 'PUT':
        $d = json_decode(file_get_contents('php://input'), true);
        $aksi = $d['aksi'] ?? 'profil';

        if ($aksi === 'password') {
            // Ganti password
            $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
            $stmt->execute([$user['id']]);
            $row = $stmt->fetch();

            if (!password_verify($d['password_lama'], $row['password'])) {
                echo json_encode(['success' => false, 'message' => 'Password lama tidak sesuai']);
                exit;
            }
            if (strlen($d['password_baru']) < 6) {
                echo json_encode(['success' => false, 'message' => 'Password baru minimal 6 karakter']);
                exit;
            }
            if ($d['password_baru'] !== $d['password_konfirmasi']) {
                echo json_encode(['success' => false, 'message' => 'Konfirmasi password tidak cocok']);
                exit;
            }

            $pdo->prepare("UPDATE users SET password = ? WHERE id = ?")
                ->execute([password_hash($d['password_baru'], PASSWORD_BCRYPT), $user['id']]);

            echo json_encode(['success' => true, 'message' => 'Password berhasil diperbarui']);

        } else {
            // Update data profil
            $pdo->prepare("UPDATE users SET nama_lengkap = ?, nomor_wa = ?, email = ? WHERE id = ?")
                ->execute([$d['nama_lengkap'], $d['nomor_wa'] ?? null, $d['email'] ?? null, $user['id']]);

            // Update session nama
            $_SESSION['nama'] = $d['nama_lengkap'];

            echo json_encode([
                'success' => true,
                'message' => 'Profil berhasil diperbarui'
            ]);
                    }
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Method tidak diizinkan']);
}
?>