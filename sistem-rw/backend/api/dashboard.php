<?php
require_once __DIR__ . '/../../backend/config/database.php';
require_once __DIR__ . '/../../backend/middleware/auth.php';

requireRW();
header('Content-Type: application/json');

$pdo = getDB();

// Total warga
$totalWarga = $pdo->query("SELECT COUNT(*) FROM warga")->fetchColumn();

// Total RT
$totalRT = 8;

// Total balita
$totalBalita = $pdo->query("SELECT COUNT(*) FROM warga WHERE kategori_usia = 'Balita'")->fetchColumn();

// Total lansia
$totalLansia = $pdo->query("SELECT COUNT(*) FROM warga WHERE kategori_usia = 'Lansia'")->fetchColumn();

// Sebaran warga per RT
$sebaranRT = [];
for ($i = 1; $i <= 8; $i++) {
    $count = $pdo->prepare("SELECT COUNT(*) FROM warga WHERE rt = ?");
    $count->execute([$i]);
    $sebaranRT[] = ['rt' => $i, 'total' => (int)$count->fetchColumn()];
}

// Statistik kategori usia
$kategori = $pdo->query("
    SELECT kategori_usia, COUNT(*) as total
    FROM warga
    GROUP BY kategori_usia
    ORDER BY FIELD(kategori_usia, 'Baruta','Balita','Anak-Anak','Remaja','WUS','Dewasa','Lansia')
")->fetchAll();

// Monitoring bulanan (bulan ini)
$bulan = date('n');
$tahun = date('Y');
$monitoring = $pdo->query("SELECT COUNT(*) as total,
    SUM(CASE WHEN kategori_usia='Lansia' THEN 1 ELSE 0 END) as lansia,
    SUM(CASE WHEN kategori_usia='WUS' THEN 1 ELSE 0 END) as wus,
    SUM(CASE WHEN kategori_usia='Remaja' THEN 1 ELSE 0 END) as remaja,
    SUM(CASE WHEN kategori_usia='Balita' THEN 1 ELSE 0 END) as balita,
    SUM(CASE WHEN kategori_usia='Baruta' THEN 1 ELSE 0 END) as baruta
    FROM warga")->fetch();

echo json_encode([
    'success' => true,
    'total_warga' => (int)$totalWarga,
    'total_rt' => $totalRT,
    'total_balita' => (int)$totalBalita,
    'total_lansia' => (int)$totalLansia,
    'sebaran_rt' => $sebaranRT,
    'kategori_usia' => $kategori,
    'monitoring' => $monitoring,
    'update_terakhir' => date('d F Y'),
]);
?>
