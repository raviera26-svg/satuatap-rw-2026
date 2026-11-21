<?php
require_once __DIR__ . '/../../backend/config/database.php';
require_once __DIR__ . '/../../backend/middleware/auth.php';

requireRW();
header('Content-Type: application/json');

$pdo = getDB();
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {

    case 'GET':
        $search = $_GET['search'] ?? '';
        $params = [];
        $where  = "WHERE role = 'rt'";

        if ($search) {
            $where .= " AND (username LIKE ? OR nama_lengkap LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }

        $stmt = $pdo->prepare("SELECT id, username, nama_lengkap, nomor_rt, nomor_wa, email, status, last_login, created_at FROM users $where ORDER BY nomor_rt");
        $stmt->execute($params);
        $users = $stmt->fetchAll();

        $totalAktif    = $pdo->query("SELECT COUNT(*) FROM users WHERE role='rt' AND status='aktif'")->fetchColumn();
        $totalNonaktif = $pdo->query("SELECT COUNT(*) FROM users WHERE role='rt' AND status='nonaktif'")->fetchColumn();
        $lastLogin     = $pdo->query("SELECT last_login FROM users WHERE role='rw' ORDER BY last_login DESC LIMIT 1")->fetchColumn();

        echo json_encode([
            'success'       => true,
            'data'          => $users,
            'total'         => count($users),
            'total_aktif'   => (int)$totalAktif,
            'total_nonaktif'=> (int)$totalNonaktif,
            'last_login'    => $lastLogin,
        ]);
        break;

    case 'POST':
        $data = json_decode(file_get_contents('php://input'), true);

        // Validasi
        if (empty($data['username']) || empty($data['password']) || empty($data['nama_lengkap']) || empty($data['nomor_rt'])) {
            jsonResponse(['success' => false, 'message' => 'Semua field wajib diisi']);
        }

        // Cek username duplikat
        $cek = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $cek->execute([$data['username']]);
        if ($cek->fetch()) {
            jsonResponse(['success' => false, 'message' => 'Username sudah digunakan']);
        }

        // Cek RT sudah punya akun
        $cekRT = $pdo->prepare("SELECT id FROM users WHERE nomor_rt = ? AND role = 'rt'");
        $cekRT->execute([$data['nomor_rt']]);
        if ($cekRT->fetch()) {
            jsonResponse(['success' => false, 'message' => 'RT ' . $data['nomor_rt'] . ' sudah memiliki akun']);
        }

        $hashedPassword = password_hash($data['password'], PASSWORD_BCRYPT);

        $stmt = $pdo->prepare("INSERT INTO users (username, password, nama_lengkap, role, nomor_rt, nomor_wa, email, status)
            VALUES (?, ?, ?, 'rt', ?, ?, ?, ?)");
        $stmt->execute([
            $data['username'],
            $hashedPassword,
            $data['nama_lengkap'],
            (int)$data['nomor_rt'],
            $data['nomor_wa'] ?? null,
            $data['email'] ?? null,
            $data['status'] ?? 'aktif',
        ]);

        jsonResponse(['success' => true, 'message' => 'Akun Ketua RT baru telah berhasil didaftarkan.']);
        break;

    case 'PUT':
        $data = json_decode(file_get_contents('php://input'), true);
        $id   = (int)($data['id'] ?? 0);
        if (!$id) jsonResponse(['success' => false, 'message' => 'ID tidak valid']);

        // Cek username duplikat (kecuali milik sendiri)
        if (!empty($data['username'])) {
            $cek = $pdo->prepare("SELECT id FROM users WHERE username = ? AND id != ?");
            $cek->execute([$data['username'], $id]);
            if ($cek->fetch()) jsonResponse(['success' => false, 'message' => 'Username sudah digunakan akun lain']);
        }

        // Cek RT duplikat (kecuali milik sendiri)
        if (!empty($data['nomor_rt'])) {
            $cekRT = $pdo->prepare("SELECT id FROM users WHERE nomor_rt = ? AND role = 'rt' AND id != ?");
            $cekRT->execute([$data['nomor_rt'], $id]);
            if ($cekRT->fetch()) jsonResponse(['success' => false, 'message' => 'RT ' . $data['nomor_rt'] . ' sudah dimiliki akun lain']);
        }

        $stmt = $pdo->prepare("UPDATE users SET nama_lengkap=?, nomor_rt=?, username=?, nomor_wa=?, email=?, status=? WHERE id=? AND role='rt'");
        $stmt->execute([
            $data['nama_lengkap'],
            (int)$data['nomor_rt'],
            $data['username'],
            $data['nomor_wa'] ?? null,
            $data['email'] ?? null,
            $data['status'] ?? 'aktif',
            $id,
        ]);

        // Reset password jika diisi
        if (!empty($data['password'])) {
            $pdo->prepare("UPDATE users SET password=? WHERE id=?")->execute([
                password_hash($data['password'], PASSWORD_BCRYPT),
                $id,
            ]);
        }

        jsonResponse(['success' => true, 'message' => 'Data akun RT berhasil diperbarui']);
        break;

    case 'DELETE':
        $id = (int)($_GET['id'] ?? 0);
        if (!$id) jsonResponse(['success' => false, 'message' => 'ID tidak valid']);
        $pdo->prepare("DELETE FROM users WHERE id=? AND role='rt'")->execute([$id]);
        jsonResponse(['success' => true, 'message' => 'Akun RT berhasil dihapus']);
        break;

    default:
        jsonResponse(['success' => false, 'message' => 'Method tidak diizinkan'], 405);
}
?>