<?php
require_once __DIR__ . '/../../backend/middleware/auth.php';
requireRW();
$user = getCurrentUser();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard — Satu Atap RW</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --navy: #1a2e5a;
            --navy-dark: #0f1e3d;
            --navy-light: #243a6e;
            --accent: #2563eb;
            --green: #059669;
            --green-light: #d1fae5;
            --red: #dc2626;
            --red-light: #fee2e2;
            --amber: #d97706;
            --amber-light: #fef3c7;
            --blue-light: #dbeafe;
            --bg: #f0f3fb;
            --sidebar-bg: #ffffff;
            --white: #ffffff;
            --text-primary: #1a2e5a;
            --text-secondary: #5a6a8a;
            --text-muted: #8a9ab8;
            --border: #e2e8f4;
            --shadow-sm: 0 1px 3px rgba(26,46,90,0.07);
            --shadow-md: 0 4px 16px rgba(26,46,90,0.10);
            --radius: 14px;
            --radius-sm: 10px;
            --sidebar-w: 230px;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: var(--bg);
            color: var(--text-primary);
            display: flex;
            min-height: 100vh;
        }

        /* SIDEBAR */
        .sidebar {
            width: var(--sidebar-w);
            background: var(--sidebar-bg);
            border-right: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0; left: 0; bottom: 0;
            z-index: 100;
        }

        .sidebar-brand {
            padding: 24px 20px 20px;
            border-bottom: 1px solid var(--border);
        }

        .sidebar-brand h2 {
            font-size: 17px;
            font-weight: 700;
            color: var(--navy);
        }

        .sidebar-brand p {
            font-size: 11px;
            color: var(--text-muted);
            margin-top: 2px;
        }

        .sidebar-nav {
            flex: 1;
            padding: 16px 12px;
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 12px;
            border-radius: var(--radius-sm);
            text-decoration: none;
            color: var(--text-secondary);
            font-size: 14px;
            font-weight: 500;
            transition: all 0.15s;
        }

        .nav-item:hover { background: var(--bg); color: var(--navy); }

        .nav-item.active {
            background: var(--navy);
            color: white;
        }

        .nav-item svg { width: 18px; height: 18px; flex-shrink: 0; }

        .sidebar-bottom {
            padding: 16px 12px;
            border-top: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        /* MAIN */
        .main {
            margin-left: var(--sidebar-w);
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* TOPBAR */
        .topbar {
            background: var(--white);
            border-bottom: 1px solid var(--border);
            padding: 0 28px;
            height: 60px;
            display: flex;
            align-items: center;
            gap: 16px;
            position: sticky;
            top: 0;
            z-index: 99;
        }

        .topbar-search {
            flex: 1;
            max-width: 400px;
            position: relative;
        }

        .topbar-search input {
            width: 100%;
            height: 38px;
            padding: 0 16px 0 40px;
            background: var(--bg);
            border: 1.5px solid var(--border);
            border-radius: 20px;
            font-family: inherit;
            font-size: 13px;
            color: var(--text-primary);
            outline: none;
            transition: border-color 0.2s;
        }

        .topbar-search input:focus { border-color: var(--navy); }

        .topbar-search svg {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            width: 15px;
            height: 15px;
        }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-left: auto;
        }

        .icon-btn {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: var(--bg);
            border: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: var(--text-secondary);
            transition: all 0.15s;
        }
        .icon-btn:hover { background: var(--navy); color: white; border-color: var(--navy); }
        .icon-btn svg { width: 16px; height: 16px; }

        .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .user-info-text { text-align: right; }
        .user-info-text .name { font-size: 13px; font-weight: 600; color: var(--navy); }
        .user-info-text .role { font-size: 11px; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; }

        .avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: var(--navy);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 13px;
        }

        /* CONTENT */
        .content {
            padding: 28px;
            flex: 1;
        }

        /* STAT CARDS */
        .stat-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
            margin-bottom: 24px;
        }

        .stat-card {
            background: var(--white);
            border-radius: var(--radius);
            padding: 20px;
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--border);
            display: flex;
            align-items: flex-start;
            gap: 16px;
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: var(--radius-sm);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .stat-icon svg { width: 22px; height: 22px; }
        .stat-icon.blue { background: #dbeafe; color: #1d4ed8; }
        .stat-icon.green { background: #d1fae5; color: #059669; }
        .stat-icon.amber { background: #fef3c7; color: #d97706; }
        .stat-icon.red { background: #fee2e2; color: #dc2626; }

        .stat-body { flex: 1; }
        .stat-label { font-size: 12px; color: var(--text-secondary); font-weight: 500; }
        .stat-value { font-size: 28px; font-weight: 700; color: var(--navy); margin: 2px 0; line-height: 1; }
        .stat-sub { font-size: 12px; color: var(--text-muted); }
        .stat-sub .up { color: var(--green); font-weight: 600; }

        /* TWO COL */
        .two-col {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            margin-bottom: 24px;
        }

        .card {
            background: var(--white);
            border-radius: var(--radius);
            padding: 22px;
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--border);
        }

        .card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 18px;
        }

        .card-title { font-size: 15px; font-weight: 700; color: var(--navy); }
        .card-subtitle { font-size: 12px; color: var(--text-muted); margin-top: 2px; }

        .card-action {
            font-size: 12px;
            color: var(--accent);
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
        }

        /* CHART BAR */
        .bar-chart {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .bar-row {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .bar-label { font-size: 12px; color: var(--text-secondary); width: 80px; flex-shrink: 0; font-weight: 500; }
        .bar-track { flex: 1; height: 8px; background: var(--bg); border-radius: 99px; overflow: hidden; }
        .bar-fill { height: 100%; border-radius: 99px; background: var(--navy); transition: width 1s ease; }
        .bar-fill.green { background: var(--green); }
        .bar-fill.amber { background: var(--amber); }
        .bar-fill.red { background: var(--red); }
        .bar-val { font-size: 12px; font-weight: 700; color: var(--navy); width: 55px; text-align: right; flex-shrink: 0; }

        /* RT CHART */
        .rt-chart {
            display: flex;
            align-items: flex-end;
            gap: 8px;
            height: 140px;
            padding-top: 16px;
        }

        .rt-bar-wrap {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 6px;
            height: 100%;
            justify-content: flex-end;
        }

        .rt-bar-num { font-size: 10px; font-weight: 700; color: var(--text-secondary); }

        .rt-bar {
            width: 100%;
            background: var(--navy);
            border-radius: 4px 4px 0 0;
            transition: height 1s ease;
            min-height: 4px;
            cursor: pointer;
            opacity: 0.85;
        }

        .rt-bar:hover { opacity: 1; }

        .rt-label { font-size: 10px; color: var(--text-muted); font-weight: 600; }

        /* MONITORING TABLE */
        .monitor-table {
            width: 100%;
            border-collapse: collapse;
        }

        .monitor-table th {
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--text-muted);
            padding: 10px 12px;
            text-align: left;
            border-bottom: 1px solid var(--border);
        }

        .monitor-table td {
            padding: 14px 12px;
            font-size: 13px;
            color: var(--text-primary);
            border-bottom: 1px solid var(--border);
        }

        .monitor-table tr:last-child td { border-bottom: none; }

        .progress-wrap {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .progress-bar {
            flex: 1;
            height: 6px;
            background: var(--bg);
            border-radius: 99px;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            border-radius: 99px;
        }

        .progress-fill.green { background: var(--green); }
        .progress-fill.blue { background: var(--accent); }
        .progress-fill.amber { background: var(--amber); }
        .progress-fill.red { background: var(--red); }

        .badge {
            display: inline-flex;
            align-items: center;
            padding: 3px 10px;
            border-radius: 99px;
            font-size: 11px;
            font-weight: 600;
        }

        .badge-green { background: var(--green-light); color: var(--green); }
        .badge-blue { background: var(--blue-light); color: var(--accent); }
        .badge-amber { background: var(--amber-light); color: var(--amber); }

        .btn-icon {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            background: var(--bg);
            border: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: var(--text-secondary);
            transition: all 0.15s;
        }
        .btn-icon:hover { background: var(--navy); color: white; border-color: var(--navy); }
        .btn-icon svg { width: 14px; height: 14px; }

        .filter-row {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-outline {
            padding: 6px 14px;
            border: 1.5px solid var(--border);
            border-radius: var(--radius-sm);
            background: white;
            font-family: inherit;
            font-size: 12px;
            font-weight: 600;
            color: var(--text-secondary);
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 6px;
            transition: all 0.15s;
        }
        .btn-outline:hover { border-color: var(--navy); color: var(--navy); }
        .btn-outline svg { width: 14px; height: 14px; }

        .btn-primary {
            padding: 8px 16px;
            background: var(--navy);
            border: none;
            border-radius: var(--radius-sm);
            font-family: inherit;
            font-size: 13px;
            font-weight: 600;
            color: white;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 6px;
            transition: background 0.15s;
            text-decoration: none;
        }
        .btn-primary:hover { background: var(--navy-dark); }
        .btn-primary svg { width: 15px; height: 15px; }

        .skeleton {
            background: linear-gradient(90deg, var(--bg) 25%, #e8ecf6 50%, var(--bg) 75%);
            background-size: 200% 100%;
            animation: shimmer 1.2s infinite;
            border-radius: 6px;
        }

        @keyframes shimmer { 0% { background-position: 200% 0; } 100% { background-position: -200% 0; } }

        .footer {
            text-align: center;
            padding: 20px 28px;
            font-size: 12px;
            color: var(--text-muted);
            border-top: 1px solid var(--border);
        }
    </style>
</head>
<body>

<?php
    // Debug message if redirected with err querystring
    $err = $_GET['err'] ?? null;
    $role = $_GET['role'] ?? null;
    if ($err):
?>
    <div style="position:fixed;top:12px;right:12px;z-index:9999;background:#fef2f2;border:1px solid #fecaca;color:#991b1b;padding:12px 14px;border-radius:10px;font-size:13px;max-width:420px;">
        <div style="font-weight:700;margin-bottom:4px;">Akses ditolak</div>
        <div>err: <?= htmlspecialchars($err) ?></div>
        <?php if ($role !== null): ?>
            <div>role: <?= htmlspecialchars($role) ?></div>
        <?php endif; ?>
        <div style="margin-top:6px;opacity:0.9">Silakan login ulang dengan akun RW yang benar.</div>
    </div>
<?php endif; ?>

<!-- SIDEBAR -->
<aside class="sidebar">

    <div class="sidebar-brand">
        <h2>Satu Atap RW</h2>
        <p>Manajemen Digital</p>
    </div>
    <nav class="sidebar-nav">
        <a href="dashboard.php" class="nav-item active">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
            Dashboard
        </a>
        <a href="datawarga.php" class="nav-item">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            Data Warga
        </a>
        <a href="monitoring.php" class="nav-item">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
            Monitoring
        </a>
        <a href="users.php" class="nav-item">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="23" y1="11" x2="17" y2="11"/><line x1="20" y1="8" x2="20" y2="14"/></svg>
            Kelola User
        </a>
    </nav>
    <div class="sidebar-bottom">
       <a href="profilrw.php" class="nav-item">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>Profil Saya</a>
        <a href="#" class="nav-item" id="btnLogout" style="color:#dc2626">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
    Keluar
</a>    
    </div>
</aside>

<!-- MAIN -->
<div class="main">
    <!-- TOPBAR -->
    <header class="topbar">
        <div class="topbar-search">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input type="text" placeholder="Cari data warga...">
        </div>
        <div class="topbar-right">
            <div class="icon-btn">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
            </div>
            <div class="icon-btn">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
            </div>
            <div class="user-info">
                <div class="user-info-text">
                    <div class="name"><?= htmlspecialchars($user['nama']) ?></div>
                    <div class="role">Ketua RW 08</div>
                </div>
                <div class="avatar"><?= strtoupper(substr($user['nama'], 0, 2)) ?></div>
            </div>
        </div>
    </header>

    <!-- CONTENT -->
    <div class="content">
        <!-- STAT CARDS -->
        <div class="stat-grid">
            <div class="stat-card">
                <div class="stat-icon blue">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                </div>
                <div class="stat-body">
                    <div class="stat-label">Total Warga</div>
                    <div class="stat-value" id="statWarga"><span class="skeleton" style="width:60px;height:28px;display:block"></span></div>
                    <div class="stat-sub"><span class="up" id="statWargaSub">+12 bulan ini</span></div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon green">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/></svg>
                </div>
                <div class="stat-body">
                    <div class="stat-label">Total RT</div>
                    <div class="stat-value" id="statRT"><span class="skeleton" style="width:40px;height:28px;display:block"></span></div>
                    <div class="stat-sub">Tersebar di wilayah RW 08</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon amber">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M12 14c-5 0-8 2-8 4v1h16v-1c0-2-3-4-8-4z"/></svg>
                </div>
                <div class="stat-body">
                    <div class="stat-label">Total Balita</div>
                    <div class="stat-value" id="statBalita"><span class="skeleton" style="width:40px;height:28px;display:block"></span></div>
                    <div class="stat-sub">Target Imunisasi</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon red">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                </div>
                <div class="stat-body">
                    <div class="stat-label">Total Lansia</div>
                    <div class="stat-value" id="statLansia"><span class="skeleton" style="width:40px;height:28px;display:block"></span></div>
                    <div class="stat-sub">Warga usia &gt; 60 tahun</div>
                </div>
            </div>
        </div>

        <!-- TWO COL: Chart RT + Kategori Usia -->
        <div class="two-col">
            <!-- Sebaran per RT -->
            <div class="card">
                <div class="card-header">
                    <div>
                        <div class="card-title">Sebaran Warga per RT</div>
                    </div>
                    <a href="data-warga.php" class="card-action">Detail RT</a>
                </div>
                <div class="rt-chart" id="rtChart">
                    <?php for($i=1;$i<=8;$i++): ?>
                    <div class="rt-bar-wrap">
                        <div class="rt-bar-num" id="rtNum<?=$i?>">0</div>
                        <div class="rt-bar" id="rtBar<?=$i?>" style="height:4px" title="RT 0<?=$i?>"></div>
                        <div class="rt-label">RT <?= str_pad($i,2,'0',STR_PAD_LEFT) ?></div>
                    </div>
                    <?php endfor; ?>
                </div>
            </div>

            <!-- Statistik Kategori Usia -->
            <div class="card">
                <div class="card-header">
                    <div>
                        <div class="card-title">Statistik Kategori Usia</div>
                    </div>
                    <div style="cursor:pointer;color:var(--text-muted)">⋯</div>
                </div>
                <div class="bar-chart" id="kategoriChart">
                    <div class="skeleton" style="height:12px;width:100%"></div>
                    <div class="skeleton" style="height:12px;width:80%"></div>
                    <div class="skeleton" style="height:12px;width:60%"></div>
                </div>
            </div>
        </div>

        <!-- Monitoring Bulanan -->
        <div class="card">
            <div class="card-header">
                <div>
                    <div class="card-title">Data Monitoring Bulanan</div>
                    <div class="card-subtitle" id="updateInfo">Update terakhir: —</div>
                </div>
                <div class="filter-row">
                    <button class="btn-outline">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg>
                        Filter RT
                    </button>
                    <button class="btn-primary" onclick="exportExcel()">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                        Export Excel
                    </button>
                </div>
            </div>
            <table class="monitor-table">
                <thead>
                    <tr>
                        <th>Nama Program</th>
                        <th>Sasaran</th>
                        <th>Partisipasi</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="monitorTable">
                    <tr><td colspan="5" style="text-align:center;color:var(--text-muted);padding:20px">Memuat data...</td></tr>
                </tbody>
            </table>
        </div>
    </div>

   <footer class="footer">© 2024 Satu Atap RW — Sistem Manajemen Digital Kelurahan.</footer>
</div>

<!-- LOGOUT CONFIRM -->
<div id="logoutOverlay" style="position:fixed;inset:0;background:rgba(15,30,61,0.45);z-index:200;display:none;align-items:center;justify-content:center;padding:20px">
    <div style="background:white;border-radius:20px;max-width:400px;padding:32px;text-align:center">
        <div style="width:60px;height:60px;border-radius:50%;background:#fee2e2;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;color:#dc2626">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
        </div>
        <div style="font-size:18px;font-weight:700;color:#1a2e5a;margin-bottom:8px">Keluar dari Sistem?</div>
        <div style="font-size:13px;color:#5a6a8a;line-height:1.6;margin-bottom:20px">Apakah Anda yakin ingin mengakhiri sesi ini? Perubahan yang belum disimpan mungkin akan hilang.</div>
        <button onclick="window.location.href='/backend/api/logout.php'" style="width:100%;padding:12px;background:#1a2e5a;border:none;border-radius:10px;color:white;font-family:inherit;font-size:14px;font-weight:600;cursor:pointer;margin-bottom:12px">Keluar Sekarang</button>
        <div style="font-size:11px;color:#8a9ab8;letter-spacing:0.5px">SATU ATAP RW • ADMIN PANEL</div>
    </div>
</div>

<script>
document.getElementById('btnLogout').addEventListener('click', function(e) {
    e.preventDefault();
    document.getElementById('logoutOverlay').style.display = 'flex';
});
document.getElementById('logoutOverlay').addEventListener('click', function(e) {
    if (e.target === this) this.style.display = 'none';
});

const BULAN = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];

async function loadDashboard() {
    try {
        const res = await fetch('/backend/api/dashboard.php');
        const d = await res.json();
        if (!d.success) return;

        // Stat cards
        document.getElementById('statWarga').textContent = d.total_warga.toLocaleString('id-ID');
        document.getElementById('statRT').textContent = d.total_rt;
        document.getElementById('statBalita').textContent = d.total_balita;
        document.getElementById('statLansia').textContent = d.total_lansia;
        document.getElementById('updateInfo').textContent = 'Update terakhir: ' + d.update_terakhir;

        // RT Bar chart
        const maxRT = Math.max(...d.sebaran_rt.map(r => r.total), 1);
        d.sebaran_rt.forEach(r => {
            const pct = (r.total / maxRT) * 120;
            const bar = document.getElementById('rtBar' + r.rt);
            const num = document.getElementById('rtNum' + r.rt);
            if (bar) { bar.style.height = Math.max(pct, 4) + 'px'; }
            if (num) { num.textContent = r.total; }
        });

        // Kategori usia
        const colorMap = {
            'Baruta': 'green', 'Balita': '', 'Anak-Anak': '',
            'Remaja': '', 'WUS': 'amber', 'Dewasa': '', 'Lansia': 'red'
        };
        const maxKat = Math.max(...d.kategori_usia.map(k => parseInt(k.total)), 1);
        const totalWarga = d.total_warga || 1;
        const katHTML = d.kategori_usia.map(k => {
            const pct = Math.round((parseInt(k.total) / totalWarga) * 100);
            const cls = colorMap[k.kategori_usia] || '';
            return `<div class="bar-row">
                <div class="bar-label">${k.kategori_usia}</div>
                <div class="bar-track"><div class="bar-fill ${cls}" style="width:${pct}%"></div></div>
                <div class="bar-val">${parseInt(k.total).toLocaleString('id-ID')} Jiwa</div>
            </div>`;
        }).join('');
        document.getElementById('kategoriChart').innerHTML = katHTML || '<p style="color:var(--text-muted);font-size:13px">Belum ada data</p>';

        // Monitoring table (dari data real)
        const m = d.monitoring;
        const programs = [
            { nama: 'Posyandu Balita Melati', sub: 'Posyandu RW 08', sasaran: m.balita + ' Balita', pct: 90, cls: 'green', status: 'Selesai', badge: 'green' },
            { nama: 'Pemeriksaan Lansia', sub: 'Layanan Door-to-door', sasaran: m.lansia + ' Lansia', pct: 45, cls: 'blue', status: 'Berlangsung', badge: 'blue' },
            { nama: 'Update Data Kematian', sub: 'Sinkronisasi Dukcapil', sasaran: 'Sistem RW', pct: 10, cls: 'amber', status: 'Menunggu', badge: 'amber' },
        ];
        document.getElementById('monitorTable').innerHTML = programs.map(p => `
            <tr>
                <td><div style="font-weight:600">${p.nama}</div><div style="font-size:11px;color:var(--text-muted)">${p.sub}</div></td>
                <td>${p.sasaran}</td>
                <td>
                    <div class="progress-wrap">
                        <div class="progress-bar"><div class="progress-fill ${p.cls}" style="width:${p.pct}%"></div></div>
                        <span style="font-size:12px;font-weight:600;color:var(--text-secondary)">${p.pct}%</span>
                    </div>
                </td>
                <td><span class="badge badge-${p.badge}">${p.status}</span></td>
                <td>
                    <button class="btn-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                    </button>
                </td>
            </tr>
        `).join('');
    } catch(e) {
        console.error(e);
    }
}

function exportExcel() {
    alert('Fitur export akan segera tersedia.');
}

loadDashboard();
</script>
</body>
</html>
