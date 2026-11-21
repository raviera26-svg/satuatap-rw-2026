<?php
require_once __DIR__ . '/../../backend/config/database.php';
require_once __DIR__ . '/../../backend/middleware/auth.php';

requireLogin();
header('Content-Type: application/json');

$pdo = getDB();
$user = getCurrentUser();
$method = $_SERVER['REQUEST_METHOD'];

$rtFilter = ($user['role'] === 'rt') ? $user['nomor_rt'] : null;

switch ($method) {

    case 'GET':
        $where = ['1=1'];
        $params = [];

        if ($rtFilter) {
            $where[] = 'rt = ?'; $params[] = $rtFilter;
        } elseif (!empty($_GET['rt'])) {
            $where[] = 'rt = ?'; $params[] = (int)$_GET['rt'];
        }
        if (!empty($_GET['search'])) {
            $where[] = '(nama_lengkap LIKE ? OR nik LIKE ?)';
            $s = '%' . $_GET['search'] . '%';
            $params[] = $s; $params[] = $s;
        }
        if (!empty($_GET['kategori'])) { $where[] = 'kategori_usia = ?'; $params[] = $_GET['kategori']; }
        if (!empty($_GET['jk']))       { $where[] = 'jenis_kelamin = ?'; $params[] = $_GET['jk']; }

        $limit  = (int)($_GET['limit'] ?? 10);
        $page   = max(1, (int)($_GET['page'] ?? 1));
        $offset = ($page - 1) * $limit;
        $whereSQL = implode(' AND ', $where);

        $total = $pdo->prepare("SELECT COUNT(*) FROM warga WHERE $whereSQL");
        $total->execute($params);
        $totalRows = (int)$total->fetchColumn();

                $stmt = $pdo->prepare("
            SELECT *
            FROM warga
            WHERE $whereSQL
            ORDER BY nama_lengkap
            LIMIT $limit OFFSET $offset
        ");
        $stmt->execute($params);
        $rows = $stmt->fetchAll();

        $statsQ = $pdo->prepare("
    SELECT
        COUNT(*) AS total_warga,
        SUM(CASE WHEN status_keluarga = 'Kepala Keluarga' THEN 1 ELSE 0 END) AS total_kk,
        SUM(CASE WHEN kategori_usia = 'Lansia' THEN 1 ELSE 0 END) AS total_lansia,
        SUM(CASE WHEN perlu_update = 1 THEN 1 ELSE 0 END) AS perlu_update
    FROM warga
    WHERE $whereSQL
");

        $statsQ->execute($params);
        $stats = $statsQ->fetch(PDO::FETCH_ASSOC);

        echo json_encode([
            'success'     => true,
            'data'        => $rows,
            'total'       => $totalRows,
            'page'        => $page,
            'limit'       => $limit,
            'total_pages' => ceil($totalRows / $limit),
            'stats'       => [
                'total_warga'  => (int)($stats['total_warga'] ?? 0),
                'total_kk'     => (int)($stats['total_kk'] ?? 0),
                'total_lansia' => (int)($stats['total_lansia'] ?? 0),
                'perlu_update' => (int)($stats['perlu_update'] ?? 0),
            ]
        ]);

        break;

    case 'POST':
    $d = json_decode(file_get_contents('php://input'), true);

    if ($rtFilter && (int)$d['rt'] !== (int)$rtFilter) {
        echo json_encode([
            'success' => false,
            'message' => 'Tidak boleh menambah warga di RT lain'
        ]);
        exit;
    }

    $cek = $pdo->prepare("SELECT id FROM warga WHERE nik = ?");
    $cek->execute([$d['nik']]);

    if ($cek->fetch()) {
        echo json_encode([
            'success' => false,
            'message' => 'NIK sudah terdaftar'
        ]);
        exit;
    }

    // Hitung umur
    $umur = date_diff(
        date_create($d['tanggal_lahir']),
        date_create('today')
    )->y;

    // Tentukan kategori usia
    if ($umur < 1) {
        $kategori = 'Baruta';
    } elseif ($umur <= 5) {
        $kategori = 'Balita';
    } elseif ($umur <= 12) {
        $kategori = 'Anak-Anak';
    } elseif ($umur <= 17) {
        $kategori = 'Remaja';
    } elseif ($umur >= 60) {
        $kategori = 'Lansia';
    } elseif (
        $d['jenis_kelamin'] === 'P'
        && $umur >= 18
        && $umur <= 50
    ) {
        $kategori = 'WUS';
    } else {
        $kategori = 'Dewasa';
    }

    $stmt = $pdo->prepare("
    INSERT INTO warga (
        nik, no_kk, status_keluarga, nama_lengkap,
        tempat_lahir, tanggal_lahir, umur, jenis_kelamin,
        alamat, rt, rw, status_domisili, status_perkawinan,
        pekerjaan, no_telepon, kategori_usia, input_oleh
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
");

$stmt->execute([
    $d['nik'], $d['no_kk'],
    $d['status_keluarga'] ?? 'Kepala Keluarga',
    $d['nama_lengkap'], $d['tempat_lahir'], $d['tanggal_lahir'],
    $umur, $d['jenis_kelamin'], $d['alamat'],
    (int)$d['rt'], (int)($d['rw'] ?? 8),
    $d['status_domisili'] ?? 'Warga Tetap',
    $d['status_perkawinan'] ?? 'Belum Kawin',
    $d['pekerjaan'] ?? '', $d['no_telepon'] ?? '',
    $kategori, $user['id']
]);

    echo json_encode([
        'success' => true,
        'message' => 'Data warga berhasil ditambahkan',
        'id' => $pdo->lastInsertId()
    ]);
    break;

        case 'PUT':
    $d = json_decode(file_get_contents('php://input'), true);

    $id = (int)($d['id'] ?? 0);

    if ($rtFilter) {
        $own = $pdo->prepare("SELECT id FROM warga WHERE id=? AND rt=?");
        $own->execute([$id, $rtFilter]);

        if (!$own->fetch()) {
            echo json_encode([
                'success' => false,
                'message' => 'Tidak memiliki akses'
            ]);
            exit;
        }
    }

    // Hitung ulang umur
    $umur = date_diff(
        date_create($d['tanggal_lahir']),
        date_create('today')
    )->y;

    // Hitung ulang kategori usia
    if ($umur < 1) {
        $kategori = 'Baruta';
    } elseif ($umur <= 5) {
        $kategori = 'Balita';
    } elseif ($umur <= 12) {
        $kategori = 'Anak-Anak';
    } elseif ($umur <= 17) {
        $kategori = 'Remaja';
    } elseif ($umur >= 60) {
        $kategori = 'Lansia';
    } elseif (
        $d['jenis_kelamin'] === 'P'
        && $umur >= 15
        && $umur <= 49
    ) {
        $kategori = 'WUS';
    } else {
        $kategori = 'Dewasa';
    }

    $stmt = $pdo->prepare("
    UPDATE warga SET
        nama_lengkap=?, tempat_lahir=?, tanggal_lahir=?,
        umur=?, kategori_usia=?, jenis_kelamin=?, alamat=?,
        rt=?, rw=?, status_domisili=?, status_perkawinan=?,
        pekerjaan=?, no_telepon=?, no_kk=?, status_keluarga=?,
        perlu_update=0
    WHERE id=?
");

$stmt->execute([
    $d['nama_lengkap'], $d['tempat_lahir'], $d['tanggal_lahir'],
    $umur, $kategori, $d['jenis_kelamin'], $d['alamat'],
    (int)$d['rt'], (int)($d['rw'] ?? 8),
    $d['status_domisili'], $d['status_perkawinan'],
    $d['pekerjaan'], $d['no_telepon'], $d['no_kk'],
    $d['status_keluarga'] ?? 'Kepala Keluarga',
    $id
]);
    

    echo json_encode([
        'success' => true,
        'message' => 'Data berhasil diperbarui'
    ]);
    break;

    case 'DELETE':
        $id = (int)($_GET['id'] ?? 0);
        if ($rtFilter) {
            $own = $pdo->prepare("SELECT id FROM warga WHERE id=? AND rt=?");
            $own->execute([$id,$rtFilter]);
            if (!$own->fetch()) { echo json_encode(['success'=>false,'message'=>'Tidak memiliki akses']); exit; }
        }
        $pdo->prepare("DELETE FROM warga WHERE id=?")->execute([$id]);
        echo json_encode(['success'=>true,'message'=>'Data berhasil dihapus']);
        break;
}
?>
