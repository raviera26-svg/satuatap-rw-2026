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
    <title>Data Warga — Satu Atap RW</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        :root {
            --navy: #1a2e5a; --navy-dark: #0f1e3d; --accent: #2563eb;
            --green: #059669; --green-light: #d1fae5;
            --red: #dc2626; --red-light: #fee2e2;
            --amber: #d97706; --amber-light: #fef3c7;
            --blue-light: #dbeafe; --teal: #0891b2; --teal-light: #cffafe;
            --purple: #7c3aed; --purple-light: #ede9fe;
            --pink: #db2777; --pink-light: #fce7f3;
            --bg: #f0f3fb; --white: #ffffff;
            --text-primary: #1a2e5a; --text-secondary: #5a6a8a; --text-muted: #8a9ab8;
            --border: #e2e8f4; --shadow-sm: 0 1px 3px rgba(26,46,90,0.07);
            --shadow-md: 0 4px 16px rgba(26,46,90,0.10);
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
        .topbar-title { font-size:16px; font-weight:700; color:var(--navy); }
        .topbar-search { flex:1; max-width:400px; position:relative; }
        .topbar-search input { width:100%; height:38px; padding:0 16px 0 40px; background:var(--bg); border:1.5px solid var(--border); border-radius:20px; font-family:inherit; font-size:13px; color:var(--text-primary); outline:none; transition:border-color 0.2s; }
        .topbar-search input:focus { border-color:var(--navy); }
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
        .page-header { display:flex; align-items:flex-start; justify-content:space-between; margin-bottom:24px; }
        .page-title { font-size:22px; font-weight:700; color:var(--navy); }
        .page-sub { font-size:13px; color:var(--text-muted); margin-top:4px; }

        /* STAT CARDS */
        .stat-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:14px; margin-bottom:24px; }
        .stat-card { background:var(--white); border-radius:var(--radius); padding:18px; box-shadow:var(--shadow-sm); border:1px solid var(--border); display:flex; align-items:center; gap:14px; }
        .stat-icon { width:46px; height:46px; border-radius:var(--radius-sm); display:flex; align-items:center; justify-content:center; flex-shrink:0; }
        .stat-icon svg { width:20px; height:20px; }
        .stat-icon.blue { background:#dbeafe; color:#1d4ed8; }
        .stat-icon.green { background:var(--green-light); color:var(--green); }
        .stat-icon.gray { background:#f1f5f9; color:#64748b; }
        .stat-icon.red { background:var(--red-light); color:var(--red); }
        .stat-label { font-size:12px; color:var(--text-secondary); font-weight:500; }
        .stat-value { font-size:22px; font-weight:700; color:var(--navy); margin-top:2px; }

        /* FILTER BAR */
        .filter-bar { background:var(--white); border-radius:var(--radius); padding:14px 18px; box-shadow:var(--shadow-sm); border:1px solid var(--border); display:flex; align-items:center; gap:10px; margin-bottom:16px; flex-wrap:wrap; }
        .filter-label { font-size:12px; font-weight:600; color:var(--text-secondary); display:flex; align-items:center; gap:4px; }
        .filter-label svg { width:14px; height:14px; }
        .filter-select { height:34px; padding:0 30px 0 10px; background:var(--white); border:1.5px solid var(--border); border-radius:8px; font-family:inherit; font-size:13px; color:var(--text-primary); outline:none; cursor:pointer; appearance:none; background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%238a9ab8' stroke-width='2'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E"); background-repeat:no-repeat; background-position:right 8px center; }
        .filter-select:focus { border-color:var(--navy); }
        .btn-reset { height:34px; padding:0 12px; background:none; border:1.5px solid var(--border); border-radius:8px; font-family:inherit; font-size:12px; font-weight:600; color:var(--text-muted); cursor:pointer; }
        .btn-reset:hover { border-color:var(--red); color:var(--red); }
        .filter-actions { margin-left:auto; display:flex; gap:8px; }
        .btn-icon-sm { width:34px; height:34px; background:var(--white); border:1.5px solid var(--border); border-radius:8px; display:flex; align-items:center; justify-content:center; cursor:pointer; color:var(--text-secondary); }
        .btn-icon-sm:hover { background:var(--navy); color:white; border-color:var(--navy); }
        .btn-icon-sm svg { width:15px; height:15px; }
        .btn-primary { padding:8px 16px; background:var(--navy); border:none; border-radius:var(--radius-sm); font-family:inherit; font-size:13px; font-weight:600; color:white; cursor:pointer; display:flex; align-items:center; gap:6px; transition:background 0.15s; text-decoration:none; height:38px; }
        .btn-primary:hover { background:var(--navy-dark); }
        .btn-primary svg { width:15px; height:15px; }

        /* TABLE */
        .table-card { background:var(--white); border-radius:var(--radius); box-shadow:var(--shadow-sm); border:1px solid var(--border); overflow:hidden; }
        table { width:100%; border-collapse:collapse; }
        thead th { font-size:11px; font-weight:600; text-transform:uppercase; letter-spacing:0.5px; color:var(--text-muted); padding:12px 16px; text-align:left; border-bottom:1px solid var(--border); background:var(--bg); white-space:nowrap; }
        tbody td { padding:14px 16px; font-size:13px; color:var(--text-primary); border-bottom:1px solid var(--border); }
        tbody tr:last-child td { border-bottom:none; }
        tbody tr:hover { background:#f8faff; }

        .warga-name { display:flex; align-items:center; gap:10px; }
        .warga-avatar { width:34px; height:34px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-weight:700; font-size:12px; color:white; flex-shrink:0; }

        .badge { display:inline-flex; align-items:center; padding:3px 10px; border-radius:99px; font-size:11px; font-weight:600; white-space:nowrap; }
        .badge-baruta   { background:#dcfce7; color:#15803d; }
        .badge-balita   { background:#dbeafe; color:#1d4ed8; }
        .badge-anak     { background:#fce7f3; color:#be185d; }
        .badge-remaja   { background:#ede9fe; color:#6d28d9; }
        .badge-wus      { background:#fef3c7; color:#b45309; }
        .badge-dewasa   { background:#f1f5f9; color:#475569; }
        .badge-lansia   { background:#fee2e2; color:#b91c1c; }

        .action-btns { display:flex; gap:6px; }
        .btn-edit { width:30px; height:30px; border-radius:6px; background:var(--blue-light); border:none; display:flex; align-items:center; justify-content:center; cursor:pointer; color:var(--accent); }
        .btn-del  { width:30px; height:30px; border-radius:6px; background:var(--red-light); border:none; display:flex; align-items:center; justify-content:center; cursor:pointer; color:var(--red); }
        .btn-edit svg, .btn-del svg { width:14px; height:14px; }
        .btn-edit:hover { background:var(--accent); color:white; }
        .btn-del:hover  { background:var(--red); color:white; }

        .pagination { display:flex; align-items:center; justify-content:space-between; padding:14px 18px; border-top:1px solid var(--border); }
        .pag-info { font-size:12px; color:var(--text-muted); }
        .pag-btns { display:flex; gap:4px; }
        .pag-btn { width:32px; height:32px; border-radius:6px; border:1.5px solid var(--border); background:white; font-family:inherit; font-size:13px; font-weight:600; color:var(--text-secondary); cursor:pointer; display:flex; align-items:center; justify-content:center; }
        .pag-btn:hover { border-color:var(--navy); color:var(--navy); }
        .pag-btn.active { background:var(--navy); color:white; border-color:var(--navy); }
        .pag-btn:disabled { opacity:0.4; cursor:default; }

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

        .section-title { font-size:13px; font-weight:700; color:var(--navy); margin-bottom:14px; padding-bottom:8px; border-bottom:1px solid var(--border); display:flex; align-items:center; gap:6px; }
        .section-title svg { width:16px; height:16px; }
        .form-grid { display:grid; grid-template-columns:1fr 1fr; gap:14px; margin-bottom:20px; }
        .form-group { display:flex; flex-direction:column; gap:6px; }
        .form-group.full { grid-column:1/-1; }
        .form-label { font-size:11px; font-weight:600; text-transform:uppercase; letter-spacing:0.5px; color:var(--text-secondary); }
        .form-control { height:42px; padding:0 12px; background:var(--bg); border:1.5px solid var(--border); border-radius:8px; font-family:inherit; font-size:13px; color:var(--text-primary); outline:none; transition:border-color 0.2s; }
        .form-control:focus { border-color:var(--navy); background:white; }
        textarea.form-control { height:80px; padding:10px 12px; resize:none; }
        .radio-group { display:flex; gap:16px; height:42px; align-items:center; }
        .radio-item { display:flex; align-items:center; gap:6px; cursor:pointer; font-size:13px; color:var(--text-primary); }
        .radio-item input { accent-color:var(--navy); width:16px; height:16px; }

        .btn-cancel { padding:10px 20px; background:none; border:1.5px solid var(--border); border-radius:var(--radius-sm); font-family:inherit; font-size:13px; font-weight:600; color:var(--text-secondary); cursor:pointer; }
        .btn-cancel:hover { border-color:var(--text-secondary); }
        .btn-save { padding:10px 24px; background:var(--navy); border:none; border-radius:var(--radius-sm); font-family:inherit; font-size:13px; font-weight:600; color:white; cursor:pointer; display:flex; align-items:center; gap:6px; }
        .btn-save:hover { background:var(--navy-dark); }
        .btn-save svg { width:15px; height:15px; }

        /* CONFIRM MODAL */
        .confirm-modal { background:white; border-radius:20px; width:100%; max-width:420px; padding:36px 32px; text-align:center; box-shadow:0 20px 60px rgba(0,0,0,0.2); }
        .confirm-icon { width:64px; height:64px; border-radius:50%; background:var(--bg); display:flex; align-items:center; justify-content:center; margin:0 auto 20px; }
        .confirm-icon svg { width:28px; height:28px; color:var(--navy); }
        .confirm-title { font-size:20px; font-weight:700; color:var(--navy); margin-bottom:10px; }
        .confirm-text { font-size:13px; color:var(--text-secondary); line-height:1.6; margin-bottom:24px; }
        .confirm-btns { display:flex; gap:12px; }
        .btn-confirm-cancel { flex:1; padding:12px; border:1.5px solid var(--border); border-radius:var(--radius-sm); background:white; font-family:inherit; font-size:14px; font-weight:600; color:var(--text-secondary); cursor:pointer; }
        .btn-confirm-ok { flex:1; padding:12px; border:none; border-radius:var(--radius-sm); background:var(--navy); font-family:inherit; font-size:14px; font-weight:600; color:white; cursor:pointer; }
        .btn-confirm-ok:hover { background:var(--navy-dark); }
        .btn-confirm-ok.red { background:var(--red); }

        /* TOAST */
        .toast { position:fixed; bottom:24px; right:24px; background:var(--navy); color:white; padding:14px 20px; border-radius:12px; font-size:13px; font-weight:600; display:flex; align-items:center; gap:10px; box-shadow:var(--shadow-md); z-index:999; transform:translateY(80px); opacity:0; transition:all 0.3s ease; }
        .toast.show { transform:translateY(0); opacity:1; }
        .toast svg { width:18px; height:18px; }
        .toast.success { background:#059669; }
        .toast.error { background:var(--red); }

        .empty-state { text-align:center; padding:40px; color:var(--text-muted); font-size:13px; }
    </style>
</head>
<body>

<aside class="sidebar">
    <div class="sidebar-brand"><h2>Satu Atap RW</h2><p>Manajemen Digital</p></div>
    <nav class="sidebar-nav">
        <a href="dashboard.php" class="nav-item">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>Dashboard
        </a>
        <a href="datawarga.php" class="nav-item active">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>Data Warga
        </a>
        <a href="monitoring.php" class="nav-item">
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
        <span class="topbar-title">Data Warga</span>
        <div class="topbar-search">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input type="text" id="searchInput" placeholder="Cari Nama atau NIK..." oninput="onSearch()">
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
                <div class="page-title">Manajemen Basis Data</div>
                <div class="page-sub">Kelola informasi kependudukan secara digital dan akurat.</div>
            </div>
            <button class="btn-primary" onclick="openModal()">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="19" y1="8" x2="19" y2="14"/><line x1="22" y1="11" x2="16" y2="11"/></svg>
                Tambah Warga Baru
            </button>
        </div>

        <!-- STATS -->
        <div class="stat-grid">
            <div class="stat-card"><div class="stat-icon blue"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg></div><div><div class="stat-label">Total Warga</div><div class="stat-value" id="sTotal">—</div></div></div>
            <div class="stat-card"><div class="stat-icon green"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg></div><div><div class="stat-label">Kepala Keluarga</div><div class="stat-value" id="sKK">—</div></div></div>
            <div class="stat-card"><div class="stat-icon gray"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg></div><div><div class="stat-label">Kategori Lansia</div><div class="stat-value" id="sLansia">—</div></div></div>
            <div class="stat-card"><div class="stat-icon red"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg></div><div><div class="stat-label">Data Perlu Update</div><div class="stat-value" id="sUpdate">—</div></div></div>
        </div>

        <!-- FILTER -->
        <div class="filter-bar">
            <div class="filter-label"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg>Filter:</div>
            <select class="filter-select" id="filterRT" onchange="loadWarga()">
                <option value="">Pilih RT (Semua)</option>
                <?php for($i=1;$i<=8;$i++) echo "<option value='$i'>RT 0$i</option>"; ?>
            </select>
            <select class="filter-select" id="filterKategori" onchange="loadWarga()">
                <option value="">Kategori Usia (Semua)</option>
                <option value="Baruta">Baruta</option>
                <option value="Balita">Balita</option>
                <option value="Anak-Anak">Anak-Anak</option>
                <option value="Remaja">Remaja</option>
                <option value="WUS">WUS</option>
                <option value="Dewasa">Dewasa</option>
                <option value="Lansia">Lansia</option>
            </select>
            <button class="btn-reset" onclick="resetFilter()">Reset Filter</button>
            <div class="filter-actions">
                <button class="btn-icon-sm" title="Download"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg></button>
                <button class="btn-icon-sm" title="Print"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg></button>
            </div>
        </div>

        <!-- TABLE -->
        <div class="table-card">
            <table>
                <thead>
                    <tr>
                        <th>NIK</th><th>Nama Lengkap</th><th>RT</th>
                        <th>Status KK</th><th>Jenis Kelamin</th><th>Umur</th><th>Kategori Usia</th><th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    <tr><td colspan="7" class="empty-state">Memuat data...</td></tr>
                </tbody>
            </table>
            <div class="pagination">
                <div class="pag-info" id="pagInfo">—</div>
                <div class="pag-btns" id="pagBtns"></div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL TAMBAH/EDIT -->
<div class="modal-overlay" id="modalOverlay">
    <div class="modal">
        <div class="modal-header">
            <div class="modal-title" id="modalTitle">Tambah Data Warga</div>
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
<div class="form-group"><label class="form-label">Status dalam Keluarga</label>
    <select class="form-control" id="fStatusKeluarga">
        <option>Kepala Keluarga</option>
        <option>Istri</option>
        <option>Anak</option>
        <option>Orang Tua</option>
        <option>Lainnya</option>
    </select>
</div>
                <div class="form-group full"><label class="form-label">Nama Lengkap *</label><input class="form-control" id="fNama" placeholder="Masukkan nama sesuai KTP"></div>
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
                Alamat & Wilayah
            </div>
            <div class="form-grid">
                <div class="form-group full"><label class="form-label">Alamat Lengkap</label><textarea class="form-control" id="fAlamat" placeholder="Nama Jalan, Blok, No Rumah"></textarea></div>
                <div class="form-group"><label class="form-label">RT</label>
                    <select class="form-control" id="fRT">
                        <?php for($i=1;$i<=8;$i++) echo "<option value='$i'>RT 00$i</option>"; ?>
                    </select>
                </div>
                <div class="form-group"><label class="form-label">RW</label><input class="form-control" id="fRW" value="008" readonly></div>
                <div class="form-group"><label class="form-label">Status Domisili</label>
                    <select class="form-control" id="fDomisili">
                        <option>Warga Tetap</option><option>Warga Sementara</option><option>Pendatang</option>
                    </select>
                </div>
            </div>

            <div class="section-title">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 13 19.79 19.79 0 0 1 1.61 4.35 2 2 0 0 1 3.6 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 9.91a16 16 0 0 0 6.16 6.16l.91-.91a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                Kontak & Status
            </div>
            <div class="form-grid">
                <div class="form-group"><label class="form-label">Nomor Telepon / WA</label><input class="form-control" id="fTelp" placeholder="+62 812xxxxxxxx"></div>
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
        <div style="width:60px;height:60px;border-radius:50%;background:#fee2e2;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;color:#dc2626">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
        </div>
        <div style="font-size:18px;font-weight:700;color:#1a2e5a;margin-bottom:8px">Keluar dari Sistem?</div>
        <div style="font-size:13px;color:#5a6a8a;line-height:1.6;margin-bottom:20px">Apakah Anda yakin ingin mengakhiri sesi ini? Perubahan yang belum disimpan mungkin akan hilang.</div>
        <button onclick="window.location.href='/backend/api/logout.php'" style="width:100%;padding:12px;background:#1a2e5a;border:none;border-radius:10px;color:white;font-family:inherit;font-size:14px;font-weight:600;cursor:pointer;margin-bottom:12px">Keluar Sekarang</button>
        <div style="font-size:11px;color:#8a9ab8;letter-spacing:0.5px">SATU ATAP RW • ADMIN PANEL</div>
    </div>
</div>

<!-- TOAST -->
<div class="toast" id="toast">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
    <span id="toastMsg"></span>
</div>

<script>
document.getElementById('btnLogout').addEventListener('click', function(e) {
    e.preventDefault();
    document.getElementById('logoutOverlay').style.display = 'flex';
});
document.getElementById('logoutOverlay').addEventListener('click', function(e) {
    if (e.target === this) this.style.display = 'none';
});

let currentPage = 1;
let searchTimeout;
let deleteId = null;

const avatarColors = ['#1a2e5a','#059669','#7c3aed','#db2777','#d97706','#0891b2','#dc2626','#065f46'];

function getInitials(name) {
    return name.split(' ').slice(0,2).map(w=>w[0]).join('').toUpperCase();
}

function getBadgeClass(kat) {
    const map = {'Baruta':'baruta','Balita':'balita','Anak-Anak':'anak','Remaja':'remaja','WUS':'wus','Dewasa':'dewasa','Lansia':'lansia'};
    return 'badge-' + (map[kat] || 'dewasa');
}

async function loadWarga(page = 1) {
    currentPage = page;
    const search = document.getElementById('searchInput').value;
    const rt = document.getElementById('filterRT').value;
    const kat = document.getElementById('filterKategori').value;

    const params = new URLSearchParams({ page, limit: 10 });
    if (search) params.set('search', search);
    if (rt) params.set('rt', rt);
    if (kat) params.set('kategori', kat);

    document.getElementById('tableBody').innerHTML = '<tr><td colspan="7" class="empty-state">Memuat...</td></tr>';

    const res = await fetch('/backend/api/warga.php?' + params);
    const d = await res.json();
    if (!d.success) return;

    // Update stats
    document.getElementById('sTotal').textContent = d.stats.total_warga.toLocaleString('id-ID');
    document.getElementById('sKK').textContent = d.stats.total_kk.toLocaleString('id-ID');
    document.getElementById('sLansia').textContent = d.stats.total_lansia;
    document.getElementById('sUpdate').textContent = d.stats.perlu_update;

    if (!d.data.length) {
        document.getElementById('tableBody').innerHTML = '<tr><td colspan="7" class="empty-state">Tidak ada data ditemukan</td></tr>';
        document.getElementById('pagInfo').textContent = '0 data';
        document.getElementById('pagBtns').innerHTML = '';
        return;
    }

    document.getElementById('tableBody').innerHTML = d.data.map((w, i) => `
        <tr>
            <td style="font-size:12px;font-family:monospace">${w.nik}</td>
            <td>
                <div class="warga-name">
                    <div class="warga-avatar" style="background:${avatarColors[i%8]}">${getInitials(w.nama_lengkap)}</div>
                    <div>
                        <div style="font-weight:600">${w.nama_lengkap}</div>
                        <div style="font-size:11px;color:var(--text-muted)">${w.pekerjaan || '—'}</div>
                    </div>
                </div>
            </td>
            <td><span style="font-weight:600">${String(w.rt).padStart(2,'0')}</span></td>
        <td><span style="font-size:11px;padding:3px 8px;border-radius:99px;background:#f1f5f9;color:#475569;font-weight:600">${w.status_keluarga || 'Kepala Keluarga'}</span></td>
<td>${w.jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan'}</td>
            <td>${w.umur}</td>
            <td><span class="badge ${getBadgeClass(w.kategori_usia)}">${w.kategori_usia}</span></td>
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
        </tr>
    `).join('');

    // Pagination
    const from = (page-1)*10+1, to = Math.min(page*10, d.total);
    document.getElementById('pagInfo').textContent = `Menampilkan ${from}-${to} dari ${d.total.toLocaleString('id-ID')} warga`;

    let btns = '';
    btns += `<button class="pag-btn" onclick="loadWarga(${page-1})" ${page<=1?'disabled':''}>‹</button>`;
    for (let p = Math.max(1,page-1); p <= Math.min(d.total_pages, page+1); p++) {
        btns += `<button class="pag-btn ${p===page?'active':''}" onclick="loadWarga(${p})">${p}</button>`;
    }
    if (d.total_pages > page+2) btns += `<button class="pag-btn" disabled>...</button>`;
    if (d.total_pages > page+1) btns += `<button class="pag-btn" onclick="loadWarga(${d.total_pages})">${d.total_pages}</button>`;
    btns += `<button class="pag-btn" onclick="loadWarga(${page+1})" ${page>=d.total_pages?'disabled':''}>›</button>`;
    document.getElementById('pagBtns').innerHTML = btns;
}

function onSearch() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => loadWarga(1), 400);
}

function resetFilter() {
    document.getElementById('filterRT').value = '';
    document.getElementById('filterKategori').value = '';
    document.getElementById('searchInput').value = '';
    loadWarga(1);
}

function openModal(title = 'Tambah Data Warga') {
    document.getElementById('modalTitle').textContent = title;
    document.getElementById('modalOverlay').classList.add('open');
}

function closeModal() {
    document.getElementById('modalOverlay').classList.remove('open');
    document.getElementById('editId').value = '';
    ['fNIK','fKK','fNama','fTempatLahir','fPekerjaan','fAlamat','fTelp'].forEach(id => document.getElementById(id).value = '');
    document.getElementById('fTglLahir').value = '';
    document.getElementById('fRT').value = '1';
    document.getElementById('fDomisili').value = 'Warga Tetap';
    document.getElementById('fKawin').value = 'Belum Kawin';
    document.getElementById('fStatusKeluarga').value = 'Kepala Keluarga';
    document.querySelectorAll('input[name="jk"]').forEach(r => r.checked = false);
}

function editWarga(w) {
    document.getElementById('editId').value = w.id;
    document.getElementById('fNIK').value = w.nik;
    document.getElementById('fNIK').readOnly = true;
    document.getElementById('fKK').value = w.no_kk;
    document.getElementById('fStatusKeluarga').value = w.status_keluarga || 'Kepala Keluarga';
    document.getElementById('fNama').value = w.nama_lengkap;
    document.getElementById('fTempatLahir').value = w.tempat_lahir;
    document.getElementById('fTglLahir').value = w.tanggal_lahir;
    document.getElementById('fPekerjaan').value = w.pekerjaan || '';
    document.getElementById('fAlamat').value = w.alamat || '';
    document.getElementById('fRT').value = w.rt;
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
    document.getElementById('confirmText').textContent = 'Pastikan data yang Anda masukkan sudah benar sebelum disimpan ke sistem.';
    document.getElementById('confirmOk').className = 'btn-confirm-ok';
    document.getElementById('confirmOk').textContent = 'Ya, Simpan Data';
    document.getElementById('confirmCancel').onclick = () => document.getElementById('confirmOverlay').classList.remove('open');
    document.getElementById('confirmOk').onclick = saveWarga;
    document.getElementById('confirmOverlay').classList.add('open');
}

function confirmDelete(id, nama) {
    deleteId = id;
    document.getElementById('confirmTitle').textContent = 'Hapus Data Warga?';
    document.getElementById('confirmText').textContent = `Data warga "${nama}" akan dihapus permanen dari sistem. Tindakan ini tidak dapat dibatalkan.`;
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
        rt: document.getElementById('fRT').value,
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
