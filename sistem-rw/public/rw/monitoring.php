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
    <title>Monitoring — Satu Atap RW</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        :root {
            --navy: #1a2e5a; --navy-dark: #0f1e3d;
            --green: #059669; --green-light: #d1fae5;
            --red: #dc2626; --red-light: #fee2e2;
            --amber: #d97706; --amber-light: #fef3c7;
            --blue: #2563eb; --blue-light: #dbeafe;
            --purple: #7c3aed; --purple-light: #ede9fe;
            --teal: #0891b2; --teal-light: #cffafe;
            --bg: #f0f3fb; --white: #ffffff;
            --text-primary: #1a2e5a; --text-secondary: #5a6a8a; --text-muted: #8a9ab8;
            --border: #e2e8f4; --shadow-sm: 0 1px 3px rgba(26,46,90,0.07);
            --radius: 14px; --radius-sm: 10px; --sidebar-w: 230px;
        }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: var(--bg); color: var(--text-primary); display: flex; min-height: 100vh; }

        /* SIDEBAR */
        .sidebar { width: var(--sidebar-w); background: var(--white); border-right: 1px solid var(--border); display: flex; flex-direction: column; position: fixed; top:0;left:0;bottom:0; z-index:100; }
        .sidebar-brand { padding: 24px 20px 20px; border-bottom: 1px solid var(--border); }
        .sidebar-brand h2 { font-size: 17px; font-weight: 700; color: var(--navy); }
        .sidebar-brand p { font-size: 11px; color: var(--text-muted); margin-top: 2px; }
        .sidebar-nav { flex:1; padding: 16px 12px; display:flex; flex-direction:column; gap:2px; }
        .nav-item { display:flex; align-items:center; gap:10px; padding:10px 12px; border-radius:var(--radius-sm); text-decoration:none; color:var(--text-secondary); font-size:14px; font-weight:500; transition:all 0.15s; }
        .nav-item:hover { background:var(--bg); color:var(--navy); }
        .nav-item.active { background:var(--navy); color:white; }
        .nav-item svg { width:18px; height:18px; flex-shrink:0; }
        .sidebar-bottom { padding:16px 12px; border-top:1px solid var(--border); display:flex; flex-direction:column; gap:2px; }

        /* MAIN */
        .main { margin-left:var(--sidebar-w); flex:1; display:flex; flex-direction:column; }
        .topbar { background:var(--white); border-bottom:1px solid var(--border); padding:0 28px; height:60px; display:flex; align-items:center; gap:16px; position:sticky; top:0; z-index:99; }
        .topbar-search { flex:1; max-width:400px; position:relative; }
        .topbar-search input { width:100%; height:38px; padding:0 16px 0 40px; background:var(--bg); border:1.5px solid var(--border); border-radius:20px; font-family:inherit; font-size:13px; outline:none; }
        .topbar-search svg { position:absolute; left:14px; top:50%; transform:translateY(-50%); color:var(--text-muted); width:15px; height:15px; }
        .topbar-right { display:flex; align-items:center; gap:12px; margin-left:auto; }
        .icon-btn { width:36px; height:36px; border-radius:50%; background:var(--bg); border:1px solid var(--border); display:flex; align-items:center; justify-content:center; cursor:pointer; color:var(--text-secondary); transition:all 0.15s; }
        .icon-btn:hover { background:var(--navy); color:white; border-color:var(--navy); }
        .icon-btn svg { width:16px; height:16px; }
        .user-info { display:flex; align-items:center; gap:10px; }
        .user-info-text .name { font-size:13px; font-weight:600; color:var(--navy); }
        .user-info-text .role { font-size:11px; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.5px; }
        .avatar { width:36px; height:36px; border-radius:50%; background:var(--navy); color:white; display:flex; align-items:center; justify-content:center; font-weight:700; font-size:13px; }

        /* CONTENT */
        .content { padding: 28px; flex:1; }
        .page-header { display:flex; align-items:flex-start; justify-content:space-between; margin-bottom:24px; flex-wrap:wrap; gap:12px; }
        .page-title { font-size:22px; font-weight:700; color:var(--navy); }
        .page-sub { font-size:13px; color:var(--text-muted); margin-top:4px; }

        /* BULAN PICKER */
        .bulan-picker { display:flex; align-items:center; gap:10px; }
        .bulan-select { height:38px; padding:0 12px; background:var(--white); border:1.5px solid var(--border); border-radius:var(--radius-sm); font-family:inherit; font-size:13px; color:var(--text-primary); outline:none; cursor:pointer; }
        .bulan-select:focus { border-color:var(--navy); }
        .btn-export { height:38px; padding:0 16px; background:var(--green); border:none; border-radius:var(--radius-sm); font-family:inherit; font-size:13px; font-weight:600; color:white; cursor:pointer; display:flex; align-items:center; gap:6px; }
        .btn-export:hover { background:#047857; }
        .btn-export svg { width:15px; height:15px; }

        /* STAT CARDS */
        .stat-grid { display:grid; grid-template-columns:repeat(5,1fr); gap:14px; margin-bottom:24px; }
        .stat-card { background:var(--white); border-radius:var(--radius); padding:18px; box-shadow:var(--shadow-sm); border:1px solid var(--border); }
        .stat-icon-row { display:flex; align-items:center; justify-content:space-between; margin-bottom:10px; }
        .stat-icon { width:40px; height:40px; border-radius:10px; display:flex; align-items:center; justify-content:center; }
        .stat-icon svg { width:18px; height:18px; }
        .stat-icon.gray  { background:#f1f5f9; color:#64748b; }
        .stat-icon.pink  { background:#fce7f3; color:#db2777; }
        .stat-icon.purple{ background:var(--purple-light); color:var(--purple); }
        .stat-icon.blue  { background:var(--blue-light); color:var(--blue); }
        .stat-icon.teal  { background:var(--teal-light); color:var(--teal); }
        .stat-change { font-size:11px; font-weight:700; padding:2px 8px; border-radius:99px; }
        .stat-change.up   { background:var(--green-light); color:var(--green); }
        .stat-change.down { background:var(--red-light); color:var(--red); }
        .stat-label { font-size:12px; color:var(--text-secondary); font-weight:500; }
        .stat-value { font-size:26px; font-weight:700; color:var(--navy); margin:4px 0 2px; line-height:1; }
        .stat-sub { width:100%; height:3px; background:var(--border); border-radius:99px; margin-top:10px; overflow:hidden; }
        .stat-sub-fill { height:100%; border-radius:99px; }

        /* TABLE CARD */
        .card { background:var(--white); border-radius:var(--radius); box-shadow:var(--shadow-sm); border:1px solid var(--border); overflow:hidden; margin-bottom:20px; }
        .card-header { display:flex; align-items:center; justify-content:space-between; padding:18px 22px; border-bottom:1px solid var(--border); }
        .card-title { font-size:15px; font-weight:700; color:var(--navy); }
        .sort-btn { display:flex; align-items:center; gap:4px; font-size:12px; color:var(--text-muted); cursor:pointer; }
        .sort-btn svg { width:14px; height:14px; }

        table { width:100%; border-collapse:collapse; }
        thead th { font-size:11px; font-weight:600; text-transform:uppercase; letter-spacing:0.5px; color:var(--text-muted); padding:12px 18px; text-align:left; background:var(--bg); border-bottom:1px solid var(--border); }
        thead th:not(:first-child) { text-align:center; }
        tbody td { padding:16px 18px; font-size:14px; color:var(--text-primary); border-bottom:1px solid var(--border); }
        tbody td:not(:first-child) { text-align:center; }
        tbody tr:last-child td { border-bottom:none; font-weight:700; color:var(--navy); background:#f8faff; }
        tbody tr:hover:not(:last-child) { background:#f8faff; }
        .rt-label { font-size:14px; font-weight:600; color:var(--navy); }

        .btn-view { width:32px; height:32px; border-radius:8px; background:var(--blue-light); border:none; display:flex; align-items:center; justify-content:center; cursor:pointer; color:var(--blue); margin:auto; }
        .btn-view:hover { background:var(--blue); color:white; }
        .btn-view svg { width:15px; height:15px; }

        /* BANNER */
        .banner { border-radius:var(--radius); overflow:hidden; background:linear-gradient(135deg, #1a2e5a 0%, #2563eb 100%); padding:32px; display:flex; align-items:center; justify-content:space-between; gap:20px; }
        .banner-text h3 { font-size:20px; font-weight:700; color:white; margin-bottom:8px; }
        .banner-text p { font-size:13px; color:rgba(255,255,255,0.75); line-height:1.6; max-width:480px; }
        .btn-banner { padding:10px 20px; background:white; border:none; border-radius:var(--radius-sm); font-family:inherit; font-size:13px; font-weight:700; color:var(--navy); cursor:pointer; white-space:nowrap; }
        .banner-img { width:180px; height:120px; background:rgba(255,255,255,0.1); border-radius:12px; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
        .banner-img svg { width:64px; height:64px; color:rgba(255,255,255,0.4); }

        .footer { text-align:center; padding:20px 28px; font-size:12px; color:var(--text-muted); border-top:1px solid var(--border); }
    </style>
</head>
<body>

<aside class="sidebar">
    <div class="sidebar-brand"><h2>Satu Atap RW</h2><p>Manajemen Digital</p></div>
    <nav class="sidebar-nav">
        <a href="dashboard.php" class="nav-item">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>Dashboard
        </a>
        <a href="datawarga.php" class="nav-item">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>Data Warga
        </a>
        <a href="monitoring.php" class="nav-item active">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>Monitoring
        </a>
        <a href="users.php" class="nav-item">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="23" y1="11" x2="17" y2="11"/><line x1="20" y1="8" x2="20" y2="14"/></svg>Kelola User
        </a>
    </nav>
    <div class="sidebar-bottom">
       <a href="profilrw.php" class="nav-item">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>Profil Saya</a>
        <a href="#" class="nav-item" id="btnLogout" style="color:#dc2626">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>Keluar
</a>
    </div>
</aside>

<div class="main">
    <header class="topbar">
        <div class="topbar-search">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input type="text" placeholder="Cari data monitoring...">
        </div>
        <div class="topbar-right">
            <div class="icon-btn"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg></div>
            <div class="icon-btn"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg></div>
            <div class="user-info">
                <div class="user-info-text"><div class="name"><?= htmlspecialchars($user['nama']) ?></div><div class="role">Ketua RW 08</div></div>
                <div class="avatar"><?= strtoupper(substr($user['nama'],0,2)) ?></div>
            </div>
        </div>
    </header>

    <div class="content">
        <div class="page-header">
            <div>
                <div class="page-title">Monitoring Bulanan</div>
                <div class="page-sub">Rekapitulasi demografi warga berdasarkan kategori khusus.</div>
            </div>
            <div class="bulan-picker">
                <select class="bulan-select" id="selBulan" onchange="loadMonitoring()">
                    <?php
                    $namaBulan = ['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
                    $bulanIni = (int)date('n');
                    for ($b = 1; $b <= 12; $b++) {
                        $sel = $b === $bulanIni ? 'selected' : '';
                        echo "<option value='$b' $sel>{$namaBulan[$b]}</option>";
                    }
                    ?>
                </select>
                <select class="bulan-select" id="selTahun" onchange="loadMonitoring()">
                    <?php for ($y = date('Y'); $y >= date('Y')-3; $y--) echo "<option value='$y'>$y</option>"; ?>
                </select>
                <button class="btn-export" onclick="alert('Fitur export segera hadir')">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                    Export Laporan
                </button>
            </div>
        </div>

        <!-- STAT CARDS -->
        <div class="stat-grid">
            <div class="stat-card">
                <div class="stat-icon-row">
                    <div class="stat-icon gray"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg></div>
                    <span class="stat-change up" id="changeLansia">+0%</span>
                </div>
                <div class="stat-label">Total Lansia</div>
                <div class="stat-value" id="valLansia">—</div>
                <div class="stat-sub"><div class="stat-sub-fill" style="background:#64748b;width:0%" id="barLansia"></div></div>
            </div>
            <div class="stat-card">
                <div class="stat-icon-row">
                    <div class="stat-icon pink"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M12 14c-5 0-8 2-8 4v1h16v-1c0-2-3-4-8-4z"/></svg></div>
                    <span class="stat-change up" id="changeWus">+0%</span>
                </div>
                <div class="stat-label">Total WUS</div>
                <div class="stat-value" id="valWus">—</div>
                <div class="stat-sub"><div class="stat-sub-fill" style="background:#db2777;width:0%" id="barWus"></div></div>
            </div>
            <div class="stat-card">
                <div class="stat-icon-row">
                    <div class="stat-icon purple"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg></div>
                    <span class="stat-change down" id="changeRemaja">-0%</span>
                </div>
                <div class="stat-label">Total Remaja</div>
                <div class="stat-value" id="valRemaja">—</div>
                <div class="stat-sub"><div class="stat-sub-fill" style="background:#7c3aed;width:0%" id="barRemaja"></div></div>
            </div>
            <div class="stat-card">
                <div class="stat-icon-row">
                    <div class="stat-icon blue"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M12 14c-5 0-8 2-8 4v1h16v-1c0-2-3-4-8-4z"/></svg></div>
                    <span class="stat-change up" id="changeBalita">+0%</span>
                </div>
                <div class="stat-label">Total Balita</div>
                <div class="stat-value" id="valBalita">—</div>
                <div class="stat-sub"><div class="stat-sub-fill" style="background:#2563eb;width:0%" id="barBalita"></div></div>
            </div>
            <div class="stat-card">
                <div class="stat-icon-row">
                    <div class="stat-icon teal"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 12l2 2 4-4"/><path d="M12 2a10 10 0 1 0 0 20 10 10 0 0 0 0-20z"/></svg></div>
                    <span class="stat-change up" id="changeBaruta">+0%</span>
                </div>
                <div class="stat-label">Total Baruta</div>
                <div class="stat-value" id="valBaruta">—</div>
                <div class="stat-sub"><div class="stat-sub-fill" style="background:#0891b2;width:0%" id="barBaruta"></div></div>
            </div>
        </div>

        <!-- TABEL REKAP PER RT -->
        <div class="card">
            <div class="card-header">
                <div class="card-title">Rekapitulasi Kategori per RT</div>
                <div class="sort-btn">Urutkan: Nomor RT <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg></div>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>RT / Unit</th>
                        <th>Lansia</th>
                        <th>Wanita Usia Subur</th>
                        <th>Remaja</th>
                        <th>Balita</th>
                        <th>Baruta</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="rekapBody">
                    <tr><td colspan="7" style="text-align:center;padding:20px;color:var(--text-muted)">Memuat data...</td></tr>
                </tbody>
            </table>
        </div>

        <!-- BANNER -->
        <div class="banner">
            <div class="banner-text">
                <h3>Wujudkan RW Sehat &amp; Terdata</h3>
                <p>Data monitoring yang akurat membantu pengurus RW dalam merencanakan program bantuan, posyandu, dan kegiatan sosial yang tepat sasaran bagi warga.</p>
                <button class="btn-banner" style="margin-top:16px" onclick="window.location.href='data-warga.php'">Lihat Detail Wilayah</button>
            </div>
            <div class="banner-img">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
            </div>
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
async function loadMonitoring() {
    const bulan = document.getElementById('selBulan').value;
    const tahun = document.getElementById('selTahun').value;

    const res = await fetch(`/backend/api/monitoring.php?bulan=${bulan}&tahun=${tahun}`);
    const d = await res.json();
    if (!d.success) return;

    const t = d.totals;
    const c = d.changes;
    const maxVal = Math.max(t.lansia, t.wus, t.remaja, t.balita, t.baruta, 1);

    // Stat cards
    const items = [
        { key:'lansia', label:'Lansia' },
        { key:'wus',    label:'WUS' },
        { key:'remaja', label:'Remaja' },
        { key:'balita', label:'Balita' },
        { key:'baruta', label:'Baruta' },
    ];

    items.forEach(item => {

    const K = item.key.charAt(0).toUpperCase() + item.key.slice(1);

    const valEl = document.getElementById('val' + K);
    const changeEl = document.getElementById('change' + K);
    const barEl = document.getElementById('bar' + K);

    console.log(K, valEl, changeEl, barEl);

    if (!valEl || !changeEl || !barEl) return;

    valEl.textContent = t[item.key];

    const chg = c[item.key];

    changeEl.textContent =
        (chg >= 0 ? '+' : '') + chg + '%';

    changeEl.className =
        'stat-change ' + (chg >= 0 ? 'up' : 'down');

    barEl.style.width =
        Math.round((t[item.key] / maxVal) * 100) + '%';
});

    // Tabel rekap RT
    if (!d.rekap_rt.length) {
        document.getElementById('rekapBody').innerHTML = '<tr><td colspan="7" style="text-align:center;padding:20px;color:var(--text-muted)">Tidak ada data</td></tr>';
        return;
    }

    const totalRow = { lansia:0, wus:0, remaja:0, balita:0, baruta:0 };
    let rows = d.rekap_rt.map(r => {
        totalRow.lansia += r.lansia;
        totalRow.wus    += r.wus;
        totalRow.remaja += r.remaja;
        totalRow.balita += r.balita;
        totalRow.baruta += r.baruta;
        return `<tr>
            <td><span class="rt-label">RT ${String(r.rt).padStart(2,'0')}</span></td>
            <td>${r.lansia}</td>
            <td>${r.wus}</td>
            <td>${r.remaja}</td>
            <td>${r.balita}</td>
            <td>${r.baruta}</td>
            <td>
                <button class="btn-view" title="Lihat Detail" onclick="window.location.href='data-warga.php?rt=${r.rt}'">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                </button>
            </td>
        </tr>`;
    });

    // Baris total
    rows.push(`<tr>
        <td>TOTAL RW</td>
        <td>${totalRow.lansia}</td>
        <td>${totalRow.wus}</td>
        <td>${totalRow.remaja}</td>
        <td>${totalRow.balita}</td>
        <td>${totalRow.baruta}</td>
        <td></td>
    </tr>`);

    document.getElementById('rekapBody').innerHTML = rows.join('');
}

loadMonitoring();
</script>
</body>
</html>