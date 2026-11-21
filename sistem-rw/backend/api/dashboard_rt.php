<?php
require_once __DIR__ . '/../../backend/config/database.php';
require_once __DIR__ . '/../../backend/middleware/auth.php';

requireRT();
header('Content-Type: application/json');

$pdo  = getDB();
$user = getCurrentUser();
$rt   = $user['nomor_rt'];

$totalWarga  = (int)$pdo->prepare("SELECT COUNT(*) FROM warga WHERE rt = ?")->execute([$rt]);
$stmt = $pdo->prepare("SELECT COUNT(*) FROM warga WHERE rt = ?");
$stmt->execute([$rt]);
$totalWarga = (int)$stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) FROM warga WHERE rt = ? AND kategori_usia = 'Lansia'");
$stmt->execute([$rt]);
$totalLansia = (int)$stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) FROM warga WHERE rt = ? AND kategori_usia IN ('Balita','Baruta','Anak-Anak')");
$stmt->execute([$rt]);
$totalAnak = (int)$stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) FROM warga WHERE rt = ? AND perlu_update = 1");
$stmt->execute([$rt]);
$perluUpdate = (int)$stmt->fetchColumn();

// Distribusi usia
$stmt = $pdo->prepare("SELECT
    SUM(CASE WHEN kategori_usia IN ('Baruta','Balita') THEN 1 ELSE 0 END) AS balita,
    SUM(CASE WHEN kategori_usia IN ('Anak-Anak','Remaja') THEN 1 ELSE 0 END) AS anak_remaja,
    SUM(CASE WHEN kategori_usia IN ('Dewasa','WUS') THEN 1 ELSE 0 END) AS dewasa,
    SUM(CASE WHEN kategori_usia = 'Lansia' THEN 1 ELSE 0 END) AS lansia
    FROM warga WHERE rt = ?");
$stmt->execute([$rt]);
$distribusi = $stmt->fetch();

// Data terbaru ditambahkan / diupdate di RT ini
$stmt = $pdo->prepare("SELECT nama_lengkap, status_keluarga, created_at, updated_at
    FROM warga WHERE rt = ? ORDER BY updated_at DESC LIMIT 5");
$stmt->execute([$rt]);
$terbaru = $stmt->fetchAll();

echo json_encode([
    'success'      => true,
    'rt'           => $rt,
    'total_warga'  => $totalWarga,
    'total_lansia' => $totalLansia,
    'total_anak'   => $totalAnak,
    'perlu_update' => $perluUpdate,
    'distribusi'   => [
        'balita'      => (int)$distribusi['balita'],
        'anak_remaja' => (int)$distribusi['anak_remaja'],
        'dewasa'      => (int)$distribusi['dewasa'],
        'lansia'      => (int)$distribusi['lansia'],
    ],
    'terbaru' => $terbaru,
]);
?>