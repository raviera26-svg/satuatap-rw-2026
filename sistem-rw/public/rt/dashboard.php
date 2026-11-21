<?php
require_once __DIR__ . '/../../backend/middleware/auth.php';
requireRT();
$user = getCurrentUser();
$rtPad = str_pad($user['nomor_rt'], 2, '0', STR_PAD_LEFT);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard RT <?= $rtPad ?> — Satu Atap RW</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        :root {
            --green: #16a34a; --green-dark: #15803d; --green-light: #dcfce7;
            --navy: #1a2e5a;
            --red: #dc2626; --red-light: #fee2e2;
            --amber: #d97706; --amber-light: #fef3c7;
            --blue: #2563eb; --blue-light: #dbeafe;
            --bg: #f0f3fb; --white: #ffffff;
            --text-primary: #1a2e5a; --text-secondary: #5a6a8a; --text-muted: #8a9ab8;
            --border: #e2e8f4; --shadow-sm: 0 1px 3px rgba(26,46,90,0.07);
            --shadow-md: 0 4px 16px rgba(26,46,90,0.10);
            --radius: 14px; --radius-sm: 10px; --sidebar-w: 230px;
        }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: var(--bg); color: var(--text-primary); display: flex; min-height: 100vh; }

        /* SIDEBAR */
        .sidebar { width: var(--sidebar-w); background: var(--white); border-right: 1px solid var(--border); display: flex; flex-direction: column; position: fixed; top:0;left:0;bottom:0; z-index:100; }
        .sidebar-brand { padding: 24px 20px 20px; border-bottom: 1px solid var(--border); display:flex; align-items:center; gap:10px; }
        .sidebar-brand-icon { width:38px; height:38px; background:var(--green); border-radius:10px; display:flex; align-items:center; justify-content:center; color:white; flex-shrink:0; }
        .sidebar-brand-icon svg { width:20px; height:20px; }
        .sidebar-brand h2 { font-size:16px; font-weight:700; color:var(--navy); }
        .sidebar-brand p { font-size:11px; color:var(--text-muted); margin-top:1px; }
        .sidebar-nav { flex:1; padding: 16px 12px; display:flex; flex-direction:column; gap:2px; }
        .nav-item { display:flex; align-items:center; gap:10px; padding:10px 12px; border-radius:var(--radius-sm); text-decoration:none; color:var(--text-secondary); font-size:14px; font-weight:500; transition:all 0.15s; }
        .nav-item:hover { background:var(--bg); color:var(--navy); }
        .nav-item.active { background:var(--green-light); color:var(--green-dark); font-weight:600; }
        .nav-item svg { width:18px; height:18px; flex-shrink:0; }
        .sidebar-bottom { padding:16px 12px; border-top:1px solid var(--border); display:flex; flex-direction:column; gap:2px; }

        /* MAIN */
        .main { margin-left:var(--sidebar-w); flex:1; display:flex; flex-direction:column; }
        .topbar { background:var(--white); border-bottom:1px solid var(--border); padding:0 28px; height:60px; display:flex; align-items:center; gap:16px; position:sticky; top:0; z-index:99; }
        .topbar-title { font-size:16px; font-weight:700; color:var(--navy); }
        .topbar-search { flex:1; max-width:380px; position:relative; margin-left:24px; }
        .topbar-search input { width:100%; height:38px; padding:0 16px 0 40px; background:var(--bg); border:1.5px solid var(--border); border-radius:20px; font-family:inherit; font-size:13px; outline:none; }
        .topbar-search svg { position:absolute; left:14px; top:50%; transform:translateY(-50%); color:var(--text-muted); width:15px; height:15px; }
        .topbar-right { display:flex; align-items:center; gap:12px; margin-left:auto; }
        .icon-btn { width:36px; height:36px; border-radius:50%; background:var(--bg); border:1px solid var(--border); display:flex; align-items:center; justify-content:center; cursor:pointer; color:var(--text-secondary); }
        .icon-btn:hover { background:var(--green); color:white; border-color:var(--green); }
        .icon-btn svg { width:16px; height:16px; }
        .user-info { display:flex; align-items:center; gap:10px; }
        .user-info-text { text-align:right; }
        .user-info-text .name { font-size:13px; font-weight:600; color:var(--navy); }
        .user-info-text .role { font-size:11px; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.5px; }
        .avatar { width:36px; height:36px; border-radius:50%; background:var(--green); color:white; display:flex; align-items:center; justify-content:center; font-weight:700; font-size:13px; }

        /* CONTENT */
        .content { padding: 28px; flex:1; }
        .page-title { font-size:22px; font-weight:700; color:var(--navy); margin-bottom:4px; }
        .page-sub { font-size:13px; color:var(--text-muted); margin-bottom:24px; }

        /* STAT CARDS */
        .stat-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:16px; margin-bottom:24px; }
        .stat-card { background:var(--white); border-radius:var(--radius); padding:20px; box-shadow:var(--shadow-sm); border:1px solid var(--border); }
        .stat-top { display:flex; align-items:center; justify-content:space-between; margin-bottom:14px; }
        .stat-icon { width:42px; height:42px; border-radius:10px; display:flex; align-items:center; justify-content:center; }
        .stat-icon svg { width:19px; height:19px; }
        .stat-icon.green { background:var(--green-light); color:var(--green-dark); }
        .stat-icon.gray  { background:#f1f5f9; color:#475569; }
        .stat-icon.teal  { background:#cffafe; color:#0891b2; }
        .stat-icon.red   { background:var(--red-light); color:var(--red); }
        .stat-pill { font-size:11px; font-weight:700; padding:3px 9px; border-radius:99px; background:var(--green-light); color:var(--green-dark); }
        .stat-pill.urgent { background:var(--red-light); color:var(--red); }
        .stat-pill.gray   { background:#f1f5f9; color:#64748b; }
        .stat-value { font-size:26px; font-weight:700; color:var(--navy); margin-bottom:2px; }
        .stat-label { font-size:12px; color:var(--text-secondary); font-weight:500; }

        /* TWO COL */
        .two-col { display:grid; grid-template-columns:1fr 1.3fr; gap:16px; margin-bottom:20px; }
        .card { background:var(--white); border-radius:var(--radius); padding:22px; box-shadow:var(--shadow-sm); border:1px solid var(--border); }
        .card-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:18px; }
        .card-title { font-size:15px; font-weight:700; color:var(--navy); }
        .card-link { font-size:12px; color:var(--green-dark); font-weight:600; text-decoration:none; }

        /* DISTRIBUSI BAR */
        .dist-row { margin-bottom:16px; }
        .dist-row:last-child { margin-bottom:0; }
        .dist-top { display:flex; justify-content:space-between; font-size:13px; margin-bottom:6px; }
        .dist-label { color:var(--text-secondary); font-weight:500; }
        .dist-val { color:var(--navy); font-weight:700; }
        .dist-track { height:8px; background:var(--bg); border-radius:99px; overflow:hidden; }
        .dist-fill { height:100%; border-radius:99px; background:var(--green); transition:width 1s ease; }
        .dist-fill.navy { background:var(--navy); }
        .dist-fill.gray { background:#94a3b8; }

        .quote-box { margin-top:20px; padding:14px; background:var(--bg); border-radius:10px; border-left:3px solid var(--green); }
        .quote-box p { font-size:12px; color:var(--text-secondary); font-style:italic; line-height:1.6; }

        /* TABLE */
        table { width:100%; border-collapse:collapse; }
        thead th { font-size:11px; font-weight:600; text-transform:uppercase; letter-spacing:0.5px; color:var(--text-muted); padding:10px 12px; text-align:left; border-bottom:1px solid var(--border); }
        tbody td { padding:12px; font-size:13px; color:var(--text-primary); border-bottom:1px solid var(--border); }
        tbody tr:last-child td { border-bottom:none; }

        .warga-cell { display:flex; align-items:center; gap:10px; }
        .warga-avatar { width:30px; height:30px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-weight:700; font-size:11px; color:white; flex-shrink:0; }

        .badge-selesai  { display:inline-flex; padding:3px 10px; border-radius:99px; font-size:11px; font-weight:600; background:var(--green-light); color:var(--green-dark); }
        .badge-diproses { display:inline-flex; padding:3px 10px; border-radius:99px; font-size:11px; font-weight:600; background:var(--blue-light); color:var(--blue); }
        .badge-ditolak  { display:inline-flex; padding:3px 10px; border-radius:99px; font-size:11px; font-weight:600; background:var(--red-light); color:var(--red); }

        /* BOTTOM ROW */
        .bottom-grid { display:grid; grid-template-columns:1.5fr 1fr 1fr; gap:16px; }
        .banner-announce { background:linear-gradient(135deg,var(--navy),#2d4a8a); border-radius:var(--radius); padding:22px; color:white; }
        .banner-announce h3 { font-size:15px; font-weight:700; margin-bottom:8px; }
        .banner-announce p { font-size:12px; color:rgba(255,255,255,0.75); line-height:1.6; margin-bottom:14px; }
        .btn-banner { padding:8px 16px; background:white; border:none; border-radius:8px; font-family:inherit; font-size:12px; font-weight:700; color:var(--navy); cursor:pointer; }

        .info-card { display:flex; flex-direction:column; gap:10px; }
        .info-card-icon { width:40px; height:40px; border-radius:10px; display:flex; align-items:center; justify-content:center; }
        .info-card-icon svg { width:18px; height:18px; }
        .info-card-value { font-size:22px; font-weight:700; color:var(--navy); }
        .info-card-label { font-size:12px; color:var(--text-secondary); }

        .footer { text-align:center; padding:20px 28px; font-size:12px; color:var(--text-muted); border-top:1px solid var(--border); }
        .footer-links { display:flex; gap:14px; justify-content:center; margin-top:6px; }
        .footer-links a { color:var(--text-muted); text-decoration:none; font-size:11px; }

        /* TOAST */
        .toast { position:fixed; bottom:24px; right:24px; padding:14px 20px; border-radius:12px; font-size:13px; font-weight:600; display:flex; align-items:center; gap:10px; box-shadow:var(--shadow-md); z-index:999; transform:translateY(80px); opacity:0; transition:all 0.3s; }
        .toast.show { transform:translateY(0); opacity:1; }
        .toast.success { background:var(--green-dark); color:white; }
        .toast.error   { background:var(--red); color:white; }
        .toast svg { width:16px; height:16px; }
    </style>
</head>
<body>

<aside class="sidebar">
    <div class="sidebar-brand">
        <div class="sidebar-brand-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
        </div>
        <div><h2>Panel RT</h2><p>Manajemen Warga</p></div>
    </div>
    <nav class="sidebar-nav">
        <a href="dashboard.php" class="nav-item active">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
            Dashboard
        </a>
        <a href="data-warga.php" class="nav-item">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            Data Warga
        </a>
    </nav>
    <div class="sidebar-bottom">
        <a href="profilrt.php" class="nav-item">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            Profil Saya
        </a>
        <a href="#" class="nav-item" id="btnLogout" style="color:#dc2626">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
            Keluar
        </a>
    </div>
</aside>

<div class="main">
    <header class="topbar">
        <span class="topbar-title">Satu Atap RT</span>
        <div class="topbar-search">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input type="text" placeholder="Cari data warga...">
        </div>
        <div class="topbar-right">
            <div class="icon-btn"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg></div>
            <div class="icon-btn"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.07 4.93a10 10 0 0 1 0 14.14M4.93 4.93a10 10 0 0 0 0 14.14"/></svg></div>
            <div class="user-info">
                <div class="user-info-text"><div class="name">Ketua RT <?= $rtPad ?></div><div class="role"><?= htmlspecialchars($user['nama']) ?></div></div>
                <div class="avatar"><?= strtoupper(substr($user['nama'],0,2)) ?></div>
            </div>
        </div>
    </header>

    <div class="content">
        <div class="page-title">Dashboard RT <?= $rtPad ?></div>
        <div class="page-sub">Ringkasan statistik dan pembaruan data warga terkini.</div>

        <!-- STAT CARDS -->
        <div class="stat-grid">
            <div class="stat-card">
                <div class="stat-top">
                    <div class="stat-icon green"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg></div>
                    <span class="stat-pill" id="pillWarga">+0 bln ini</span>
                </div>
                <div class="stat-value" id="valWarga">—</div>
                <div class="stat-label">Total Warga</div>
            </div>
            <div class="stat-card">
                <div class="stat-top">
                    <div class="stat-icon gray"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg></div>
                    <span class="stat-pill gray" id="pillLansia">0% total</span>
                </div>
                <div class="stat-value" id="valLansia">—</div>
                <div class="stat-label">Kategori Lansia</div>
            </div>
            <div class="stat-card">
                <div class="stat-top">
                    <div class="stat-icon teal"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M12 14c-5 0-8 2-8 4v1h16v-1c0-2-3-4-8-4z"/></svg></div>
                    <span class="stat-pill gray" id="pillAnak">0% total</span>
                </div>
                <div class="stat-value" id="valAnak">—</div>
                <div class="stat-label">Kategori Anak &amp; Balita</div>
            </div>
            <div class="stat-card">
                <div class="stat-top">
                    <div class="stat-icon red"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg></div>
                    <span class="stat-pill urgent" id="pillUpdate">Urgent</span>
                </div>
                <div class="stat-value" id="valUpdate">—</div>
                <div class="stat-label">Data Perlu Update</div>
            </div>
        </div>

        <!-- TWO COL -->
        <div class="two-col">
            <!-- Distribusi Usia -->
            <div class="card">
                <div class="card-header">
                    <div class="card-title">Distribusi Usia</div>
                    <div style="cursor:pointer;color:var(--text-muted)">⋯</div>
                </div>
                <div class="dist-row">
                    <div class="dist-top"><span class="dist-label">Balita (0-5)</span><span class="dist-val" id="distBalita">0</span></div>
                    <div class="dist-track"><div class="dist-fill" id="barBalita" style="width:0%"></div></div>
                </div>
                <div class="dist-row">
                    <div class="dist-top"><span class="dist-label">Anak &amp; Remaja (6-17)</span><span class="dist-val" id="distAnak">0</span></div>
                    <div class="dist-track"><div class="dist-fill navy" id="barAnak" style="width:0%"></div></div>
                </div>
                <div class="dist-row">
                    <div class="dist-top"><span class="dist-label">Dewasa (18-59)</span><span class="dist-val" id="distDewasa">0</span></div>
                    <div class="dist-track"><div class="dist-fill gray" id="barDewasa" style="width:0%"></div></div>
                </div>
                <div class="dist-row">
                    <div class="dist-top"><span class="dist-label">Lansia (60+)</span><span class="dist-val" id="distLansia">0</span></div>
                    <div class="dist-track"><div class="dist-fill" style="background:var(--red)" id="barLansia" style="width:0%"></div></div>
                </div>
                <div class="quote-box">
                    <p id="quoteText">Memuat insight wilayah...</p>
                </div>
            </div>

            <!-- Pembaruan Data -->
            <div class="card">
                <div class="card-header">
                    <div class="card-title">Pembaruan Data Terkini</div>
                    <a href="data-warga.php" class="card-link">Lihat Semua</a>
                </div>
                <table>
                    <thead><tr><th>Nama Warga</th><th>Status Keluarga</th><th>Tanggal</th></tr></thead>
                    <tbody id="terbaruBody">
                        <tr><td colspan="3" style="text-align:center;color:var(--text-muted);padding:16px">Memuat data...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- BOTTOM ROW -->
        <div class="bottom-grid">
            <div class="banner-announce">
                <h3>Selamat Datang, Ketua RT <?= $rtPad ?> 👋</h3>
                <p>Kelola data kependudukan warga RT Anda secara digital. Semua perubahan akan tersinkronisasi otomatis dengan sistem RW.</p>
                <button class="btn-banner" onclick="window.location.href='data-warga.php'">Kelola Data Warga</button>
            </div>
            <div class="card info-card">
                <div class="info-card-icon" style="background:var(--green-light);color:var(--green-dark)">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                </div>
                <div class="info-card-value" id="cardKK">—</div>
                <div class="info-card-label">Kepala Keluarga Terdaftar</div>
            </div>
            <div class="card info-card">
                <div class="info-card-icon" style="background:var(--blue-light);color:var(--blue)">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                </div>
                <div class="info-card-value" id="cardWilayah">RT <?= $rtPad ?></div>
                <div class="info-card-label">Wilayah Anda Kelola</div>
            </div>
        </div>
    </div>

    <footer class="footer">
        © 2024 Satu Atap RW — Digunakan oleh RT <?= $rtPad ?>.
        <div class="footer-links"><a href="#">Syarat &amp; Ketentuan</a><a href="#">Kebijakan Privasi</a></div>
    </footer>
</div>

<!-- LOGOUT CONFIRM -->
<div id="logoutOverlay" style="position:fixed;inset:0;background:rgba(15,30,61,0.45);z-index:200;display:none;align-items:center;justify-content:center;padding:20px">
    <div style="background:white;border-radius:20px;max-width:400px;padding:32px;text-align:center">
        <div style="width:60px;height:60px;border-radius:50%;background:var(--red-light);display:flex;align-items:center;justify-content:center;margin:0 auto 16px;color:var(--red)">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
        </div>
        <div style="font-size:18px;font-weight:700;color:var(--navy);margin-bottom:8px">Keluar dari Sistem?</div>
        <div style="font-size:13px;color:var(--text-secondary);line-height:1.6;margin-bottom:20px">Apakah Anda yakin ingin mengakhiri sesi ini? Perubahan yang belum disimpan mungkin akan hilang.</div>
        <button onclick="window.location.href='/backend/api/logout.php'" style="width:100%;padding:12px;background:var(--navy);border:none;border-radius:10px;color:white;font-family:inherit;font-size:14px;font-weight:600;cursor:pointer;margin-bottom:12px">Keluar Sekarang</button>
        <div style="font-size:11px;color:var(--text-muted);letter-spacing:0.5px">SATU ATAP RW • PANEL RT</div>
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

const avatarColors = ['#16a34a','#1a2e5a','#7c3aed','#db2777','#0891b2'];

function getInitials(name) {
    return name.split(' ').slice(0,2).map(w=>w[0]).join('').toUpperCase();
}

async function loadDashboard() {
    const res = await fetch('/backend/api/dashboard_rt.php');
    const d = await res.json();
    if (!d.success) return;

    document.getElementById('valWarga').textContent  = d.total_warga.toLocaleString('id-ID');
    document.getElementById('valLansia').textContent = d.total_lansia;
    document.getElementById('valAnak').textContent   = d.total_anak;
    document.getElementById('valUpdate').textContent = d.perlu_update;

    const total = Math.max(d.total_warga, 1);
    document.getElementById('pillLansia').textContent = Math.round(d.total_lansia/total*100) + '% total';
    document.getElementById('pillAnak').textContent    = Math.round(d.total_anak/total*100) + '% total';
    document.getElementById('pillUpdate').className = d.perlu_update > 0 ? 'stat-pill urgent' : 'stat-pill gray';
    document.getElementById('pillUpdate').textContent = d.perlu_update > 0 ? 'Urgent' : 'Aman';

    // Distribusi
    const dist = d.distribusi;
    const maxDist = Math.max(dist.balita, dist.anak_remaja, dist.dewasa, dist.lansia, 1);
    document.getElementById('distBalita').textContent = dist.balita;
    document.getElementById('distAnak').textContent   = dist.anak_remaja;
    document.getElementById('distDewasa').textContent = dist.dewasa;
    document.getElementById('distLansia').textContent = dist.lansia;
    document.getElementById('barBalita').style.width = (dist.balita/maxDist*100) + '%';
    document.getElementById('barAnak').style.width    = (dist.anak_remaja/maxDist*100) + '%';
    document.getElementById('barDewasa').style.width  = (dist.dewasa/maxDist*100) + '%';
    document.getElementById('barLansia').style.width  = (dist.lansia/maxDist*100) + '%';

    // Quote dinamis
    const dominan = Math.max(dist.balita, dist.anak_remaja, dist.dewasa, dist.lansia);
    let quote = 'Data wilayah RT ' + d.rt + ' sudah tercatat dengan baik.';
    if (dominan === dist.dewasa) quote = `Mayoritas warga RT ${String(d.rt).padStart(2,'0')} berada di rentang usia produktif. Perlu perhatian khusus pada program posyandu balita bulan depan.`;
    else if (dominan === dist.lansia) quote = `RT ${String(d.rt).padStart(2,'0')} memiliki populasi lansia yang cukup tinggi. Pertimbangkan program kesehatan lansia rutin.`;
    else if (dominan === dist.balita) quote = `RT ${String(d.rt).padStart(2,'0')} memiliki banyak balita. Pastikan jadwal imunisasi dan posyandu berjalan baik.`;
    document.getElementById('quoteText').textContent = quote;

    // Kepala Keluarga
    const wRes = await fetch('/backend/api/warga.php?limit=1');
    const wD = await wRes.json();
    if (wD.success) document.getElementById('cardKK').textContent = wD.stats.total_kk;

    // Tabel terbaru
    if (!d.terbaru.length) {
        document.getElementById('terbaruBody').innerHTML = '<tr><td colspan="3" style="text-align:center;color:var(--text-muted);padding:16px">Belum ada data</td></tr>';
        return;
    }

    document.getElementById('terbaruBody').innerHTML = d.terbaru.map((t,i) => {
        const date = new Date(t.updated_at);
        const tgl = date.getDate() + ' ' + ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'][date.getMonth()] + ' ' + date.getFullYear();
        return `<tr>
            <td>
                <div class="warga-cell">
                    <div class="warga-avatar" style="background:${avatarColors[i%5]}">${getInitials(t.nama_lengkap)}</div>
                    <span style="font-weight:600">${t.nama_lengkap}</span>
                </div>
            </td>
            <td>${t.status_keluarga || 'Kepala Keluarga'}</td>
            <td>${tgl}</td>
        </tr>`;
    }).join('');
}

loadDashboard();
</script>
</body>
</html>