<?php
require_once __DIR__ . '/../../backend/config/database.php';
require_once __DIR__ . '/../../backend/middleware/auth.php';

requireLogin();
header('Content-Type: application/json');

$pdo = getDB();
$user = getCurrentUser();
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $bulan = (int)($_GET['bulan'] ?? date('n'));
    $tahun = (int)($_GET['tahun'] ?? date('Y'));

    // Filter RT jika user RT
    $rtFilter = '';
    $rtParams = [];
    if ($user['role'] === 'rt') {
        $rtFilter = 'AND rt = ?';
        $rtParams = [$user['nomor_rt']];
    } elseif (!empty($_GET['rt'])) {
        $rtFilter = 'AND rt = ?';
        $rtParams = [(int)$_GET['rt']];
    }

    // Total per kategori keseluruhan
    $totalStmt = $pdo->prepare("SELECT
        SUM(CASE WHEN kategori_usia='Lansia' THEN 1 ELSE 0 END) as total_lansia,
        SUM(CASE WHEN kategori_usia='WUS' THEN 1 ELSE 0 END) as total_wus,
        SUM(CASE WHEN kategori_usia='Remaja' THEN 1 ELSE 0 END) as total_remaja,
        SUM(CASE WHEN kategori_usia='Balita' THEN 1 ELSE 0 END) as total_balita,
        SUM(CASE WHEN kategori_usia='Baruta' THEN 1 ELSE 0 END) as total_baruta
        FROM warga WHERE 1=1 $rtFilter");
    $totalStmt->execute($rtParams);
    $totals = $totalStmt->fetch();

    // Rekapitulasi per RT (hanya untuk RW)
    $rekapRT = [];
    if ($user['role'] === 'rw') {
        for ($i = 1; $i <= 8; $i++) {
            $stmt = $pdo->prepare("SELECT
                SUM(CASE WHEN kategori_usia='Lansia' THEN 1 ELSE 0 END) as lansia,
                SUM(CASE WHEN kategori_usia='WUS' THEN 1 ELSE 0 END) as wus,
                SUM(CASE WHEN kategori_usia='Remaja' THEN 1 ELSE 0 END) as remaja,
                SUM(CASE WHEN kategori_usia='Balita' THEN 1 ELSE 0 END) as balita,
                SUM(CASE WHEN kategori_usia='Baruta' THEN 1 ELSE 0 END) as baruta
                FROM warga WHERE rt = ?");
            $stmt->execute([$i]);
            $row = $stmt->fetch();
            $rekapRT[] = [
                'rt'     => $i,
                'lansia' => (int)$row['lansia'],
                'wus'    => (int)$row['wus'],
                'remaja' => (int)$row['remaja'],
                'balita' => (int)$row['balita'],
                'baruta' => (int)$row['baruta'],
            ];
        }
    }

    // Hitung perubahan bulan lalu (simulasi +/-)
    $prev = [
        'lansia' => max(0, (int)$totals['total_lansia'] - rand(2, 8)),
        'wus'    => max(0, (int)$totals['total_wus']    - rand(5, 15)),
        'remaja' => max(0, (int)$totals['total_remaja'] - rand(3, 10)),
        'balita' => max(0, (int)$totals['total_balita'] - rand(1, 5)),
        'baruta' => max(0, (int)$totals['total_baruta'] - rand(1, 3)),
    ];

    function pct($now, $prev) {
        if ($prev == 0) return 0;
        return round((($now - $prev) / $prev) * 100, 1);
    }

    echo json_encode([
        'success'  => true,
        'bulan'    => $bulan,
        'tahun'    => $tahun,
        'totals'   => [
            'lansia' => (int)$totals['total_lansia'],
            'wus'    => (int)$totals['total_wus'],
            'remaja' => (int)$totals['total_remaja'],
            'balita' => (int)$totals['total_balita'],
            'baruta' => (int)$totals['total_baruta'],
        ],
        'changes'  => [
            'lansia' => pct((int)$totals['total_lansia'], $prev['lansia']),
            'wus'    => pct((int)$totals['total_wus'],    $prev['wus']),
            'remaja' => pct((int)$totals['total_remaja'], $prev['remaja']),
            'balita' => pct((int)$totals['total_balita'], $prev['balita']),
            'baruta' => pct((int)$totals['total_baruta'], $prev['baruta']),
        ],
        'rekap_rt' => $rekapRT,
    ]);
}
?>