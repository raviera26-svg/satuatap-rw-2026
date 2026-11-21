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
    <title>Data Warga RT <?= $rtPad ?> — Satu Atap RW</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        :root {
            --green: #16a34a; --green-dark: #15803d; --green-light: #dcfce7;
            --navy: #1a2e5a; --accent: #2563eb;
            --red: #dc2626; --red-light: #fee2e2;
            --amber: #d97706; --amber-light: #fef3c7;
            --blue-light: #dbeafe;
            --bg: #f0f3fb; --white: #ffffff;
            --text-primary: #1a2e5a; --text-secondary: #5a6a8a; --text-muted: #8a9ab8;
            --border: #e2e8f4; --shadow-sm: 0 1px 3px rgba(26,46,90,0.07);
            --shadow-md: 0 4px 16px rgba(26,46,90,0.10);
            --radius: 14px; --radius-sm: 10px; --sidebar-w: 230px;
        }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: var(--bg); color: var(--text-primary); display: flex; min-height: 100vh; }

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

        .main { margin-left:var(--sidebar-w); flex:1; display:flex; flex-direction:column; }
        .topbar { background:var(--white); border-bottom:1px solid var(--border); padding:0 28px; height:60px; display:flex; align-items:center; gap:16px; position:sticky; top:0; z-index:99; }
        .topbar-title { font-size:16px; font-weight:700; color:var(--navy); }
        .topbar-search { flex:1; max-width:380px; position:relative; }
        .topbar-search input { width:100%; height:38px; padding:0 16px 0 40px; background:var(--bg); border:1.5px solid var(--border); border-radius:20px; font-family:inherit; font-size:13px; outline:none; }
        .topbar-search svg { position:absolute; left:14px; top:50%; transform:translateY(-50%); color:var(--text-muted); width:15px; height:15px; }
        .topbar-right { display:flex; align-items:center; gap:12px; margin-left:auto; }
        .icon-btn { width:36px; height:36px; border-radius:50%; background:var(--bg); border:1px solid var(--border); display:flex; align-items:center; justify-content:center; cursor:pointer; color:var(--text-secondary); }
        .icon-btn:hover { background:var(--green); color:white; border-color:var(--green); }
        .icon-btn svg { width:16px; height:16px; }
        .avatar { width:36px; height:36px; border-radius:50%; background:var(--green); color:white; display:flex; align-items:center; justify-content:center; font-weight:700; font-size:13px; }

        .content { padding:28px; flex:1; }
        .page-header { display:flex; align-items:flex-start; justify-content:space-between; margin-bottom:18px; }
        .page-title-wrap .eyebrow { font-size:12px; font-weight:700; color:var(--green-dark); text-transform:uppercase; letter-spacing:0.5px; margin-bottom:4px; }
        .page-title { font-size:20px; font-weight:700; color:var(--navy); }
        .page-sub { font-size:13px; color:var(--text-muted); margin-top:4px; max-width:500px; }

        .btn-primary { padding:10px 18px; background:var(--green); border:none; border-radius:var(--radius-sm); font-family:inherit; font-size:13px; font-weight:600; color:white; cursor:pointer; display:flex; align-items:center; gap:6px; transition:background 0.15s; }
        .btn-primary:hover { background:var(--green-dark); }
        .btn-primary svg { width:16px; height:16px; }

        /* FILTER + TOTAL CARD ROW */
        .filter-row { display:grid; grid-template-columns:1fr 280px; gap:14px; margin-bottom:18px; }
        .filter-card { background:var(--white); border-radius:var(--radius); padding:18px; box-shadow:var(--shadow-sm); border:1px solid var(--border); display:flex; align-items:flex-end; gap:12px; flex-wrap:wrap; }
        .filter-group { display:flex; flex-direction:column; gap:6px; flex:1; min-width:140px; }
        .filter-label { font-size:12px; font-weight:600; color:var(--text-secondary); }
        .filter-select { height:40px; padding:0 30px 0 12px; background:var(--bg); border:1.5px solid var(--border); border-radius:8px; font-family:inherit; font-size:13px; color:var(--text-primary); outline:none; cursor:pointer; appearance:none; background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%238a9ab8' stroke-width='2'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E"); background-repeat:no-repeat; background-position:right 10px center; }
        .filter-select:focus { border-color:var(--green); }
        .btn-filter { height:40px; padding:0 18px; background:var(--blue-light); color:var(--accent); border:none; border-radius:8px; font-family:inherit; font-size:13px; font-weight:600; cursor:pointer; display:flex; align-items:center; gap:6px; white-space:nowrap; }
        .btn-filter:hover { background:var(--accent); color:white; }
        .btn-filter svg { width:14px; height:14px; }

        .total-card { background:var(--navy); border-radius:var(--radius); padding:18px 20px; color:white; display:flex; flex-direction:column; justify-content:center; position:relative; overflow:hidden; }
        .total-card::after { content:''; position:absolute; bottom:-30px; right:-20px; width:100px; height:100px; border-radius:50%; background:rgba(255,255,255,0.06); }
        .total-card .lbl { font-size:12px; color:rgba(255,255,255,0.7); }
        .total-card .val { font-size:30px; font-weight:700; margin:4px 0; }
        .total-card .trend { font-size:11px; color:#86efac; font-weight:600; }

        /* TABLE */
        .table-card { background:var(--white); border-radius:var(--radius); box-shadow:var(--shadow-sm); border:1px solid var(--border); overflow:hidden; }
        .table-card-header { display:flex; align-items:center; justify-content:space-between; padding:16px 20px; border-bottom:1px solid var(--border); }
        .table-card-title { font-size:14px; font-weight:700; color:var(--green-dark); }
        .table-icon-btns { display:flex; gap:8px; }
        .btn-icon-sm { width:32px; height:32px; background:var(--bg); border:1px solid var(--border); border-radius:8px; display:flex; align-items:center; justify-content:center; cursor:pointer; color:var(--text-secondary); }
        .btn-icon-sm:hover { background:var(--green); color:white; border-color:var(--green); }
        .btn-icon-sm svg { width:14px; height:14px; }

        table { width:100%; border-collapse:collapse; }
        thead th { font-size:11px; font-weight:600; text-transform:uppercase; letter-spacing:0.5px; color:var(--text-muted); padding:12px 16px; text-align:left; border-bottom:1px solid var(--border); background:var(--bg); white-space:nowrap; }
        tbody td { padding:14px 16px; font-size:13px; color:var(--text-primary); border-bottom:1px solid var(--border); }
        tbody tr:last-child td { border-bottom:none; }
        tbody tr:hover { background:#f8faff; }

        .warga-name-cell .nama { font-weight:600; }
        .warga-name-cell .sub { font-size:11px; color:var(--text-muted); margin-top:1px; }

        .badge { display:inline-flex; align-items:center; padding:3px 10px; border-radius:99px; font-size:11px; font-weight:600; white-space:nowrap; }
        .badge-baruta   { background:#dcfce7; color:#15803d; }
        .badge-balita   { background:#dbeafe; color:#1d4ed8; }
        .badge-anak     { background:#fce7f3; color:#be185d; }
        .badge-remaja   { background:#ede9fe; color:#6d28d9; }
        .badge-wus      { background:#fef3c7; color:#b45309; }
        .badge-dewasa   { background:#f1f5f9; color:#475569; }
        .badge-lansia   { background:#fee2e2; color:#b91c1c; }

        .badge-status   { display:inline-flex; align-items:center; gap:5px; padding:3px 10px; border-radius:99px; font-size:11px; font-weight:600; }
        .badge-status.aktif  { background:var(--green-light); color:var(--green-dark); }
        .badge-status.pindah { background:#f1f5f9; color:#64748b; }
        .badge-dot { width:5px; height:5px; border-radius:50%; background:currentColor; }

        .action-btns { display:flex; gap:6px; }
        .btn-edit { width:30px; height:30px; border-radius:6px; background:var(--blue-light); border:none; display:flex; align-items:center; justify-content:center; cursor:pointer; color:var(--accent); }
        .btn-del  { width:30px; height:30px; border-radius:6px; background:var(--red-light); border:none; display:flex; align-items:center; justify-content:center; cursor:pointer; color:var(--red); }
        .btn-edit:hover { background:var(--accent); color:white; }
        .btn-del:hover  { background:var(--red); color:white; }
        .btn-edit svg, .btn-del svg { width:14px; height:14px; }

        .pagination { display:flex; align-items:center; justify-content:space-between; padding:14px 20px; border-top:1px solid var(--border); }
        .pag-info { font-size:12px; color:var(--text-muted); }
        .pag-btns { display:flex; gap:8px; }
        .btn-pag { height:36px; padding:0 16px; border:1.5px solid var(--border); border-radius:8px; background:white; font-family:inherit; font-size:13px; font-weight:600; color:var(--text-secondary); cursor:pointer; }
        .btn-pag:hover:not(:disabled) { border-color:var(--green); color:var(--green-dark); }
        .btn-pag.primary { background:var(--navy); color:white; border-color:var(--navy); }
        .btn-pag:disabled { opacity:0.4; cursor:default; }

        .empty-state { text-align:center; padding:40px; color:var(--text-muted); font-size:13px; }

        /* MODAL */
        .modal-overlay { position:fixed; inset:0; background:rgba(15,30,61,0.45); z-index:200; display:none; align-items:center; justify-content:center; padding:20px; }
        .modal-overlay.open { display:flex; }
        .modal { background:white; border-radius:20px; width:100%; max-width:680px; max-height:90vh; overflow-y:auto; box-shadow:0 20px 60px rgba(0,0,0,0.2); }
        .modal-header { padding:24px 28px 0; display:flex; align-items:center; justify-content:space-between; }
        .modal-title { font-size:18px; font-weight:700; color:var(--navy); }
        .modal-close { width:32px; height:32px; border-radius:8px; background:var(--bg); border:none; cursor:pointer; display:flex; align-items:center; justify-content:center; color:var(--text-muted); }
        .modal-close:hover { background:var(--red-light); color:var(--red); }
        .modal-close svg { width:16px; height:16px; }
        .modal-body { padding:24px 28px; }
        .modal-footer { padding:0 28px 24px; display:flex; gap:10px; justify-content:flex-end; }

        .section-title { font-size:13px; font-weight:700; color:var(--green-dark); margin-bottom:14px; padding-bottom:8px; border-bottom:1px solid var(--border); display:flex; align-items:center; gap:6px; }
        .section-title svg { width:16px; height:16px; }
        .form-grid { display:grid; grid-template-columns:1fr 1fr; gap:14px; margin-bottom:20px; }
        .form-group { display:flex; flex-direction:column; gap:6px; }
        .form-group.full { grid-column:1/-1; }
        .form-label { font-size:11px; font-weight:600; text-transform:uppercase; letter-spacing:0.5px; color:var(--text-secondary); }
        .form-control { height:42px; padding:0 12px; background:var(--bg); border:1.5px solid var(--border); border-radius:8px; font-family:inherit; font-size:13px; color:var(--text-primary); outline:none; transition:border-color 0.2s; }
        .form-control:focus { border-color:var(--green); background:white; }
        .form-control:disabled { opacity:0.6; }
        textarea.form-control { height:80px; padding:10px 12px; resize:none; }
        .radio-group { display:flex; gap:16px; height:42px; align-items:center; }
        .radio-item { display:flex; align-items:center; gap:6px; cursor:pointer; font-size:13px; color:var(--text-primary); }
        .radio-item input { accent-color:var(--green); width:16px; height:16px; }

        .btn-cancel { padding:10px 20px; background:none; border:1.5px solid var(--border); border-radius:var(--radius-sm); font-family:inherit; font-size:13px; font-weight:600; color:var(--text-secondary); cursor:pointer; }
        .btn-cancel:hover { border-color:var(--text-secondary); }
        .btn-save { padding:10px 24px; background:var(--green); border:none; border-radius:var(--radius-sm); font-family:inherit; font-size:13px; font-weight:600; color:white; cursor:pointer; display:flex; align-items:center; gap:6px; }
        .btn-save:hover { background:var(--green-dark); }
        .btn-save svg { width:15px; height:15px; }

        /* CONFIRM MODAL */
        .confirm-modal { background:white; border-radius:20px; width:100%; max-width:420px; padding:36px 32px; text-align:center; }
        .confirm-icon { width:64px; height:64px; border-radius:50%; background:var(--bg); display:flex; align-items:center; justify-content:center; margin:0 auto 20px; }
        .confirm-icon svg { width:28px; height:28px; color:var(--green-dark); }
        .confirm-title { font-size:20px; font-weight:700; color:var(--navy); margin-bottom:10px; }
        .confirm-text { font-size:13px; color:var(--text-secondary); line-height:1.6; margin-bottom:24px; }
        .confirm-btns { display:flex; gap:12px; }
        .btn-confirm-cancel { flex:1; padding:12px; border:1.5px solid var(--border); border-radius:var(--radius-sm); background:white; font-family:inherit; font-size:14px; font-weight:600; color:var(--text-secondary); cursor:pointer; }
        .btn-confirm-ok { flex:1; padding:12px; border:none; border-radius:var(--radius-sm); background:var(--green); font-family:inherit; font-size:14px; font-weight:600; color:white; cursor:pointer; }
        .btn-confirm-ok:hover { background:var(--green-dark); }
        .btn-confirm-ok.red { background:var(--red); }

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
        <a href="dashboard.php" class="nav-item">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
            Dashboard
        </a>
        <a href="data-warga.php" class="nav-item active">
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
        <span class="topbar-title">Wilayah RT <?= $rtPad ?></span>
        <div class="topbar-search">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input type="text" id="searchInput" placeholder="Cari NIK atau Nama..." oninput="onSearch()">
        </div>
        <div class="topbar-right">
            <div class="icon-btn"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg></div>
            <div class="icon-btn"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.07 4.93a10 10 0 0 1 0 14.14M4.93 4.93a10 10 0 0 0 0 14.14"/></svg></div>
            <div class="avatar"><?= strtoupper(substr($user['nama'],0,2)) ?></div>
        </div>
    </header>

    <div class="content">
        <div class="page-header">
            <div class="page-title-wrap">
                <div class="page-title">Manajemen Data Warga</div>
                <div class="page-sub">Kelola informasi kependudukan RT <?= $rtPad ?> Kelurahan Digital secara real-time.</div>
            </div>
            <button class="btn-primary" onclick="openModal()">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="19" y1="8" x2="19" y2="14"/><line x1="22" y1="11" x2="16" y2="11"/></svg>
                Tambah Warga Baru
            </button>
        </div>

        <!-- FILTER + TOTAL -->
        <div class="filter-row">
            <div class="filter-card">
                <div class="filter-group">
                    <label class="filter-label">Kategori Usia</label>
                    <select class="filter-select" id="filterKategori">
                        <option value="">Semua Kategori</option>
                        <option value="Baruta">Baruta</option>
                        <option value="Balita">Balita</option>
                        <option value="Anak-Anak">Anak-Anak</option>
                        <option value="Remaja">Remaja</option>
                        <option value="WUS">WUS</option>
                        <option value="Dewasa">Dewasa</option>
                        <option value="Lansia">Lansia</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label class="filter-label">Status Keaktifan</label>
                    <select class="filter-select" id="filterStatus">
                        <option value="">Semua Status</option>
                        <option value="Warga Tetap">Aktif</option>
                        <option value="Pendatang">Pindah</option>
                    </select>
                </div>
                <button class="btn-filter" onclick="loadWarga(1)">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg>
                    Terapkan Filter
                </button>
            </div>
            <div class="total-card">
                <span class="lbl">Total Warga RT <?= $rtPad ?></span>
                <span class="val" id="sTotal">—</span>
                <span class="trend" id="sTrend">↗ +0 Bulan Ini</span>
            </div>
        </div>

        <!-- TABLE -->
        <div class="table-card">
            <div class="table-card-header">
                <div class="table-card-title">Daftar Kependudukan</div>
                <div class="table-icon-btns">
                    <button class="btn-icon-sm" title="Download"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg></button>
                    <button class="btn-icon-sm" title="Print"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg></button>
                </div>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>NIK</th><th>Nama Lengkap</th><th>Alamat</th>
                        <th>L/P</th><th>Kategori Usia</th><th>Status</th><th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    <tr><td colspan="7" class="empty-state">Memuat data...</td></tr>
                </tbody>
            </table>
            <div class="pagination">
                <div class="pag-info" id="pagInfo">—</div>
                <div class="pag-btns">
                    <button class="btn-pag" id="btnPrev" onclick="prevPage()">Sebelumnya</button>
                    <button class="btn-pag primary" id="btnNext" onclick="nextPage()">Selanjutnya</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL TAMBAH/EDIT -->
<div class="modal-overlay" id="modalOverlay">
    <div class="modal">
        <div class="modal-header">
            <div class="modal-title" id="modalTitle">Tambah Warga Baru - RT <?= $rtPad ?></div>
            <button class="modal-close" onclick="closeModal()"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>
        </div>
        <div class="modal-body">
            <input type="hidden" id="editId">

            <div class="section-title">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                Informasi Pribadi
            </div>
            <div class="form-grid">
                <div class="form-group"><label class="form-label">NIK *</label><input class="form-control" id="fNIK" placeholder="16 digit NIK" maxlength="16"></div>
                <div class="form-group"><label class="form-label">Nomor KK *</label><input class="form-control" id="fKK" placeholder="16 digit No. KK" maxlength="16"></div>
                <div class="form-group full"><label class="form-label">Nama Lengkap *</label><input class="form-control" id="fNama" placeholder="Masukkan nama sesuai KTP"></div>
                <div class="form-group"><label class="form-label">Status dalam Keluarga</label>
                    <select class="form-control" id="fStatusKeluarga">
                        <option>Kepala Keluarga</option>
                        <option>Istri</option>
                        <option>Anak</option>
                        <option>Orang Tua</option>
                        <option>Lainnya</option>
                    </select>
                </div>
                <div class="form-group"><label class="form-label">Tempat Lahir</label><input class="form-control" id="fTempatLahir" placeholder="Kota Kelahiran"></div>
                <div class="form-group"><label class="form-label">Tanggal Lahir *</label><input type="date" class="form-control" id="fTglLahir"></div>
                <div class="form-group"><label class="form-label">Jenis Kelamin *</label>
                    <div class="radio-group">
                        <label class="radio-item"><input type="radio" name="jk" value="L"> Laki-laki</label>
                        <label class="radio-item"><input type="radio" name="jk" value="P"> Perempuan</label>
                    </div>
                </div>
                <div class="form-group"><label class="form-label">Pekerjaan</label><input class="form-control" id="fPekerjaan" placeholder="Contoh: PNS, Karyawan Swasta"></div>
            </div>

            <div class="section-title">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg>
                Alamat &amp; Wilayah
            </div>
            <div class="form-grid">
                <div class="form-group full"><label class="form-label">Alamat Lengkap</label><textarea class="form-control" id="fAlamat" placeholder="Blok, No. Rumah"></textarea></div>
                <div class="form-group"><label class="form-label">RT</label><input class="form-control" value="RT <?= $rtPad ?>" disabled></div>
                <div class="form-group"><label class="form-label">Status Domisili</label>
                    <select class="form-control" id="fDomisili">
                        <option>Warga Tetap</option><option>Warga Sementara</option><option>Pendatang</option>
                    </select>
                </div>
            </div>

            <div class="section-title">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 13 19.79 19.79 0 0 1 1.61 4.35 2 2 0 0 1 3.6 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 9.91a16 16 0 0 0 6.16 6.16l.91-.91a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                Kontak &amp; Status
            </div>
            <div class="form-grid">
                <div class="form-group"><label class="form-label">Nomor WhatsApp</label><input class="form-control" id="fTelp" placeholder="+62 812xxxxxxxx"></div>
                <div class="form-group"><label class="form-label">Status Perkawinan</label>
                    <select class="form-control" id="fKawin">
                        <option>Belum Kawin</option><option>Kawin</option><option>Cerai Hidup</option><option>Cerai Mati</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn-cancel" onclick="closeModal()">Batal</button>
            <button class="btn-save" onclick="showConfirm()">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                Simpan Data Warga
            </button>
        </div>
    </div>
</div>

<!-- CONFIRM MODAL -->
<div class="modal-overlay" id="confirmOverlay">
    <div class="confirm-modal">
        <div class="confirm-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
        </div>
        <div class="confirm-title" id="confirmTitle">Simpan Data Warga?</div>
        <div class="confirm-text" id="confirmText">Pastikan data yang Anda masukkan sudah benar sebelum disimpan ke sistem.</div>
        <div class="confirm-btns">
            <button class="btn-confirm-cancel" id="confirmCancel">Batal</button>
            <button class="btn-confirm-ok" id="confirmOk">Ya, Simpan Data</button>
        </div>
    </div>
</div>

<!-- LOGOUT CONFIRM -->
<div id="logoutOverlay" style="position:fixed;inset:0;background:rgba(15,30,61,0.45);z-index:200;display:none;align-items:center;justify-content:center;padding:20px">
    <div style="background:white;border-radius:20px;max-width:400px;padding:32px;text-align:center">
        <div style="width:60px;height:60px;border-radius:50%;background:var(--red-light);display:flex;align-items:center;justify-content:center;margin:0 auto 16px;color:var(--red)">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
        </div>
        <div style="font-size:18px;font-weight:700;color:var(--navy);margin-bottom:8px">Keluar dari Sistem?</div>
        <div style="font-size:13px;color:var(--text-secondary);line-height:1.6;margin-bottom:20px">Apakah Anda yakin ingin mengakhiri sesi ini?</div>
        <button onclick="window.location.href='/backend/api/logout.php'" style="width:100%;padding:12px;background:var(--navy);border:none;border-radius:10px;color:white;font-family:inherit;font-size:14px;font-weight:600;cursor:pointer">Keluar Sekarang</button>
    </div>
</div>

<!-- TOAST -->
<div class="toast" id="toast">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
    <span id="toastMsg"></span>
</div>

<script>
let currentPage = 1;
let totalPages = 1;
let searchTimeout;
let deleteId = null;

document.getElementById('btnLogout').addEventListener('click', function(e) {
    e.preventDefault();
    document.getElementById('logoutOverlay').style.display = 'flex';
});
document.getElementById('logoutOverlay').addEventListener('click', function(e) {
    if (e.target === this) this.style.display = 'none';
});

function getBadgeClass(kat) {
    const map = {'Baruta':'baruta','Balita':'balita','Anak-Anak':'anak','Remaja':'remaja','WUS':'wus','Dewasa':'dewasa','Lansia':'lansia'};
    return 'badge-' + (map[kat] || 'dewasa');
}

async function loadWarga(page = 1) {
    currentPage = page;
    const search   = document.getElementById('searchInput').value;
    const kategori  = document.getElementById('filterKategori').value;
    const statusDom = document.getElementById('filterStatus').value;

    const params = new URLSearchParams({ page, limit: 10 });
    if (search)   params.set('search', search);
    if (kategori) params.set('kategori', kategori);

    document.getElementById('tableBody').innerHTML = '<tr><td colspan="7" class="empty-state">Memuat...</td></tr>';

    const res = await fetch('/backend/api/warga.php?' + params);
    const d   = await res.json();
    if (!d.success) return;

    document.getElementById('sTotal').textContent = d.stats.total_warga.toLocaleString('id-ID');
    totalPages = d.total_pages;

    if (!d.data.length) {
        document.getElementById('tableBody').innerHTML = '<tr><td colspan="7" class="empty-state">Tidak ada data ditemukan</td></tr>';
        document.getElementById('pagInfo').textContent = '0 data';
        return;
    }

    document.getElementById('tableBody').innerHTML = d.data.map(w => {
        const isAktif = w.status_domisili !== 'Pendatang';
        return `<tr>
            <td style="font-size:12px;font-family:monospace">${w.nik}</td>
            <td class="warga-name-cell">
                <div class="nama">${w.nama_lengkap}</div>
                <div class="sub">${w.status_keluarga || 'Kepala Keluarga'}</div>
            </td>
            <td>${w.alamat || '—'}</td>
            <td>${w.jenis_kelamin}</td>
            <td><span class="badge ${getBadgeClass(w.kategori_usia)}">${w.kategori_usia}</span></td>
            <td><span class="badge-status ${isAktif?'aktif':'pindah'}"><span class="badge-dot"></span>${isAktif?'Aktif':'Pindah'}</span></td>
            <td>
                <div class="action-btns">
                    <button class="btn-edit" onclick="editWarga(${JSON.stringify(w).replace(/"/g,'&quot;')})" title="Edit">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                    </button>
                    <button class="btn-del" onclick="confirmDelete(${w.id},'${w.nama_lengkap}')" title="Hapus">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
                    </button>
                </div>
            </td>
        </tr>`;
    }).join('');

    const from = (page-1)*10+1, to = Math.min(page*10, d.total);
    document.getElementById('pagInfo').textContent = `Menampilkan ${to-from+1} dari ${d.total} warga`;
    document.getElementById('btnPrev').disabled = page <= 1;
    document.getElementById('btnNext').disabled = page >= totalPages;
}

function prevPage() { if (currentPage > 1) loadWarga(currentPage - 1); }
function nextPage() { if (currentPage < totalPages) loadWarga(currentPage + 1); }

function onSearch() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => loadWarga(1), 400);
}

function openModal(title) {
    document.getElementById('modalTitle').textContent = title || 'Tambah Warga Baru - RT <?= $rtPad ?>';
    document.getElementById('modalOverlay').classList.add('open');
}

function closeModal() {
    document.getElementById('modalOverlay').classList.remove('open');
    document.getElementById('editId').value = '';
    ['fNIK','fKK','fNama','fTempatLahir','fPekerjaan','fAlamat','fTelp'].forEach(id => document.getElementById(id).value = '');
    document.getElementById('fTglLahir').value = '';
    document.getElementById('fStatusKeluarga').value = 'Kepala Keluarga';
    document.getElementById('fDomisili').value = 'Warga Tetap';
    document.getElementById('fKawin').value = 'Belum Kawin';
    document.querySelectorAll('input[name="jk"]').forEach(r => r.checked = false);
}

function editWarga(w) {
    document.getElementById('editId').value = w.id;
    document.getElementById('fNIK').value = w.nik;
    document.getElementById('fNIK').readOnly = true;
    document.getElementById('fKK').value = w.no_kk;
    document.getElementById('fNama').value = w.nama_lengkap;
    document.getElementById('fStatusKeluarga').value = w.status_keluarga || 'Kepala Keluarga';
    document.getElementById('fTempatLahir').value = w.tempat_lahir;
    document.getElementById('fTglLahir').value = w.tanggal_lahir;
    document.getElementById('fPekerjaan').value = w.pekerjaan || '';
    document.getElementById('fAlamat').value = w.alamat || '';
    document.getElementById('fDomisili').value = w.status_domisili;
    document.getElementById('fTelp').value = w.no_telepon || '';
    document.getElementById('fKawin').value = w.status_perkawinan;
    document.querySelector(`input[name="jk"][value="${w.jenis_kelamin}"]`).checked = true;
    openModal('Edit Data Warga');
}

function showConfirm() {
    const nama = document.getElementById('fNama').value.trim();
    if (!nama) { showToast('Nama lengkap wajib diisi', 'error'); return; }
    const isEdit = !!document.getElementById('editId').value;
    document.getElementById('confirmTitle').textContent = isEdit ? 'Simpan Perubahan?' : 'Simpan Data Warga?';
    document.getElementById('confirmOk').className = 'btn-confirm-ok';
    document.getElementById('confirmOk').textContent = 'Ya, Simpan Data';
    document.getElementById('confirmCancel').onclick = () => document.getElementById('confirmOverlay').classList.remove('open');
    document.getElementById('confirmOk').onclick = saveWarga;
    document.getElementById('confirmOverlay').classList.add('open');
}

function confirmDelete(id, nama) {
    deleteId = id;
    document.getElementById('confirmTitle').textContent = 'Hapus Data Warga?';
    document.getElementById('confirmText').textContent = `Data warga "${nama}" akan dihapus permanen. Tindakan ini tidak dapat dibatalkan.`;
    document.getElementById('confirmOk').className = 'btn-confirm-ok red';
    document.getElementById('confirmOk').textContent = 'Ya, Hapus Data';
    document.getElementById('confirmCancel').onclick = () => document.getElementById('confirmOverlay').classList.remove('open');
    document.getElementById('confirmOk').onclick = deleteWarga;
    document.getElementById('confirmOverlay').classList.add('open');
}

async function saveWarga() {
    document.getElementById('confirmOverlay').classList.remove('open');
    const editId = document.getElementById('editId').value;
    const jk = document.querySelector('input[name="jk"]:checked')?.value;
    if (!jk) { showToast('Jenis kelamin wajib dipilih', 'error'); return; }

    const body = {
        nik: document.getElementById('fNIK').value,
        no_kk: document.getElementById('fKK').value,
        status_keluarga: document.getElementById('fStatusKeluarga').value,
        nama_lengkap: document.getElementById('fNama').value,
        tempat_lahir: document.getElementById('fTempatLahir').value,
        tanggal_lahir: document.getElementById('fTglLahir').value,
        jenis_kelamin: jk,
        pekerjaan: document.getElementById('fPekerjaan').value,
        alamat: document.getElementById('fAlamat').value,
        rt: <?= $user['nomor_rt'] ?>,
        rw: 8,
        status_domisili: document.getElementById('fDomisili').value,
        no_telepon: document.getElementById('fTelp').value,
        status_perkawinan: document.getElementById('fKawin').value,
    };

    if (editId) body.id = editId;

    const method = editId ? 'PUT' : 'POST';
    const res = await fetch('/backend/api/warga.php', { method, headers: {'Content-Type':'application/json'}, body: JSON.stringify(body) });
    const d = await res.json();

    if (d.success) {
        showToast(d.message, 'success');
        closeModal();
        loadWarga(currentPage);
    } else {
        showToast(d.message || 'Gagal menyimpan data', 'error');
    }
}

async function deleteWarga() {
    document.getElementById('confirmOverlay').classList.remove('open');
    const res = await fetch('/backend/api/warga.php?id=' + deleteId, { method: 'DELETE' });
    const d = await res.json();
    if (d.success) { showToast(d.message, 'success'); loadWarga(currentPage); }
    else showToast(d.message || 'Gagal menghapus', 'error');
}

function showToast(msg, type = 'success') {
    const t = document.getElementById('toast');
    t.className = 'toast ' + type;
    document.getElementById('toastMsg').textContent = msg;
    t.classList.add('show');
    setTimeout(() => t.classList.remove('show'), 3500);
}

loadWarga();
</script>
</body>
</html>