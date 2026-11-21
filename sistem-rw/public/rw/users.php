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
    <title>Kelola User — Satu Atap RW</title>
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
            --bg: #f0f3fb; --white: #ffffff;
            --text-primary: #1a2e5a; --text-secondary: #5a6a8a; --text-muted: #8a9ab8;
            --border: #e2e8f4; --shadow-sm: 0 1px 3px rgba(26,46,90,0.07);
            --shadow-md: 0 4px 16px rgba(26,46,90,0.10);
            --radius: 14px; --radius-sm: 10px; --sidebar-w: 230px;
        }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: var(--bg); color: var(--text-primary); display: flex; min-height: 100vh; }

        .sidebar { width:var(--sidebar-w); background:var(--white); border-right:1px solid var(--border); display:flex; flex-direction:column; position:fixed; top:0;left:0;bottom:0; z-index:100; }
        .sidebar-brand { padding:24px 20px 20px; border-bottom:1px solid var(--border); }
        .sidebar-brand h2 { font-size:17px; font-weight:700; color:var(--navy); }
        .sidebar-brand p { font-size:11px; color:var(--text-muted); margin-top:2px; }
        .sidebar-nav { flex:1; padding:16px 12px; display:flex; flex-direction:column; gap:2px; }
        .nav-item { display:flex; align-items:center; gap:10px; padding:10px 12px; border-radius:var(--radius-sm); text-decoration:none; color:var(--text-secondary); font-size:14px; font-weight:500; transition:all 0.15s; }
        .nav-item:hover { background:var(--bg); color:var(--navy); }
        .nav-item.active { background:var(--navy); color:white; }
        .nav-item svg { width:18px; height:18px; flex-shrink:0; }
        .sidebar-bottom { padding:16px 12px; border-top:1px solid var(--border); display:flex; flex-direction:column; gap:2px; }

        .main { margin-left:var(--sidebar-w); flex:1; display:flex; flex-direction:column; }
        .topbar { background:var(--white); border-bottom:1px solid var(--border); padding:0 28px; height:60px; display:flex; align-items:center; gap:16px; position:sticky; top:0; z-index:99; }
        .topbar-search { flex:1; max-width:400px; position:relative; }
        .topbar-search input { width:100%; height:38px; padding:0 16px 0 40px; background:var(--bg); border:1.5px solid var(--border); border-radius:20px; font-family:inherit; font-size:13px; outline:none; transition:border-color 0.2s; }
        .topbar-search input:focus { border-color:var(--navy); }
        .topbar-search svg { position:absolute; left:14px; top:50%; transform:translateY(-50%); color:var(--text-muted); width:15px; height:15px; }
        .topbar-right { display:flex; align-items:center; gap:12px; margin-left:auto; }
        .icon-btn { width:36px; height:36px; border-radius:50%; background:var(--bg); border:1px solid var(--border); display:flex; align-items:center; justify-content:center; cursor:pointer; color:var(--text-secondary); }
        .icon-btn:hover { background:var(--navy); color:white; border-color:var(--navy); }
        .icon-btn svg { width:16px; height:16px; }
        .user-info { display:flex; align-items:center; gap:10px; }
        .user-info-text .name { font-size:13px; font-weight:600; color:var(--navy); }
        .user-info-text .role { font-size:11px; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.5px; }
        .avatar { width:36px; height:36px; border-radius:50%; background:var(--navy); color:white; display:flex; align-items:center; justify-content:center; font-weight:700; font-size:13px; }

        .content { padding:28px; flex:1; }
        .page-header { display:flex; align-items:flex-start; justify-content:space-between; margin-bottom:24px; }
        .page-title { font-size:22px; font-weight:700; color:var(--navy); }
        .page-sub { font-size:13px; color:var(--text-muted); margin-top:4px; }

        .btn-primary { padding:10px 18px; background:var(--navy); border:none; border-radius:var(--radius-sm); font-family:inherit; font-size:13px; font-weight:600; color:white; cursor:pointer; display:flex; align-items:center; gap:6px; text-decoration:none; transition:background 0.15s; }
        .btn-primary:hover { background:var(--navy-dark); }
        .btn-primary svg { width:16px; height:16px; }

        /* STAT CARDS */
        .stat-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:14px; margin-bottom:24px; }
        .stat-card { background:var(--white); border-radius:var(--radius); padding:20px; box-shadow:var(--shadow-sm); border:1px solid var(--border); display:flex; align-items:center; gap:16px; }
        .stat-icon { width:48px; height:48px; border-radius:var(--radius-sm); display:flex; align-items:center; justify-content:center; flex-shrink:0; }
        .stat-icon svg { width:22px; height:22px; }
        .stat-icon.blue  { background:#dbeafe; color:#1d4ed8; }
        .stat-icon.green { background:var(--green-light); color:var(--green); }
        .stat-icon.red   { background:var(--red-light); color:var(--red); }
        .stat-icon.gray  { background:#f1f5f9; color:#475569; }
        .stat-label { font-size:12px; color:var(--text-secondary); font-weight:500; }
        .stat-value { font-size:28px; font-weight:700; color:var(--navy); margin-top:2px; }

        /* TABLE CARD */
        .card { background:var(--white); border-radius:var(--radius); box-shadow:var(--shadow-sm); border:1px solid var(--border); overflow:hidden; margin-bottom:20px; }
        .card-header { display:flex; align-items:center; justify-content:space-between; padding:18px 22px; border-bottom:1px solid var(--border); }
        .card-title { font-size:15px; font-weight:700; color:var(--navy); }
        .card-actions { display:flex; gap:8px; }
        .btn-outline { height:34px; padding:0 14px; background:var(--white); border:1.5px solid var(--border); border-radius:8px; font-family:inherit; font-size:12px; font-weight:600; color:var(--text-secondary); cursor:pointer; display:flex; align-items:center; gap:6px; }
        .btn-outline:hover { border-color:var(--navy); color:var(--navy); }
        .btn-outline svg { width:13px; height:13px; }

        table { width:100%; border-collapse:collapse; }
        thead th { font-size:11px; font-weight:600; text-transform:uppercase; letter-spacing:0.5px; color:var(--text-muted); padding:12px 18px; text-align:left; background:var(--bg); border-bottom:1px solid var(--border); }
        tbody td { padding:14px 18px; font-size:13px; color:var(--text-primary); border-bottom:1px solid var(--border); }
        tbody tr:last-child td { border-bottom:none; }
        tbody tr:hover { background:#f8faff; }

        .user-cell { display:flex; align-items:center; gap:10px; }
        .user-avatar { width:34px; height:34px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-weight:700; font-size:12px; color:white; flex-shrink:0; }

        .badge-aktif    { display:inline-flex; align-items:center; gap:5px; padding:3px 10px; border-radius:99px; font-size:11px; font-weight:600; background:var(--green-light); color:var(--green); }
        .badge-nonaktif { display:inline-flex; align-items:center; gap:5px; padding:3px 10px; border-radius:99px; font-size:11px; font-weight:600; background:var(--red-light); color:var(--red); }
        .badge-dot { width:6px; height:6px; border-radius:50%; background:currentColor; }

        .action-btns { display:flex; gap:6px; }
        .btn-reset-pw { width:30px; height:30px; border-radius:6px; background:#fef3c7; border:none; display:flex; align-items:center; justify-content:center; cursor:pointer; color:#d97706; }
        .btn-edit     { width:30px; height:30px; border-radius:6px; background:var(--blue-light); border:none; display:flex; align-items:center; justify-content:center; cursor:pointer; color:var(--blue); }
        .btn-reset-pw:hover { background:#d97706; color:white; }
        .btn-edit:hover     { background:var(--blue); color:white; }
        .btn-reset-pw svg, .btn-edit svg { width:14px; height:14px; }

        .pagination { display:flex; align-items:center; justify-content:space-between; padding:14px 18px; border-top:1px solid var(--border); font-size:12px; color:var(--text-muted); }
        .pag-btns { display:flex; gap:4px; }
        .pag-btn { width:30px; height:30px; border-radius:6px; border:1.5px solid var(--border); background:white; font-family:inherit; font-size:13px; font-weight:600; color:var(--text-secondary); cursor:pointer; display:flex; align-items:center; justify-content:center; }
        .pag-btn.active { background:var(--navy); color:white; border-color:var(--navy); }

        /* INFO CARD */
        .info-card { background:var(--white); border-radius:var(--radius); padding:22px; box-shadow:var(--shadow-sm); border:1px solid var(--border); display:flex; align-items:flex-start; gap:16px; }
        .info-icon { width:44px; height:44px; border-radius:50%; background:var(--blue-light); display:flex; align-items:center; justify-content:center; flex-shrink:0; color:var(--blue); }
        .info-icon svg { width:20px; height:20px; }
        .info-title { font-size:15px; font-weight:700; color:var(--navy); margin-bottom:6px; }
        .info-text { font-size:13px; color:var(--text-secondary); line-height:1.6; }
        .info-link { font-size:13px; font-weight:700; color:var(--navy); text-decoration:none; margin-left:auto; white-space:nowrap; }

        /* MODAL */
        .modal-overlay { position:fixed; inset:0; background:rgba(15,30,61,0.45); z-index:200; display:none; align-items:center; justify-content:center; padding:20px; }
        .modal-overlay.open { display:flex; }

        /* FORM MODAL */
        .modal { background:white; border-radius:20px; width:100%; display:flex; gap:0; overflow:hidden; box-shadow:0 20px 60px rgba(0,0,0,0.2); }
        .modal-form { flex:1; padding:28px; overflow-y:auto; max-height:90vh; }
        .modal-side { width:260px; background:var(--bg); padding:24px; border-left:1px solid var(--border); display:flex; flex-direction:column; gap:16px; }
        .modal-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:20px; }
        .modal-title { font-size:18px; font-weight:700; color:var(--navy); }
        .modal-sub { font-size:12px; color:var(--text-muted); margin-top:3px; }
        .modal-close { width:32px; height:32px; border-radius:8px; background:var(--bg); border:none; cursor:pointer; display:flex; align-items:center; justify-content:center; color:var(--text-muted); }
        .modal-close:hover { background:var(--red-light); color:var(--red); }
        .modal-close svg { width:16px; height:16px; }

        .section-title { font-size:12px; font-weight:700; color:var(--navy); margin-bottom:12px; padding-bottom:8px; border-bottom:1px solid var(--border); display:flex; align-items:center; gap:6px; }
        .section-title svg { width:14px; height:14px; }
        .form-grid { display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-bottom:18px; }
        .form-group { display:flex; flex-direction:column; gap:5px; }
        .form-group.full { grid-column:1/-1; }
        .form-label { font-size:11px; font-weight:600; text-transform:uppercase; letter-spacing:0.5px; color:var(--text-secondary); }
        .form-control { height:40px; padding:0 12px; background:var(--bg); border:1.5px solid var(--border); border-radius:8px; font-family:inherit; font-size:13px; color:var(--text-primary); outline:none; transition:border-color 0.2s; }
        .form-control:focus { border-color:var(--navy); background:white; }
        .form-control:disabled { opacity:0.6; cursor:not-allowed; }
        .toggle-wrap { display:flex; align-items:center; gap:10px; }
        .toggle { width:44px; height:24px; background:var(--border); border-radius:99px; cursor:pointer; position:relative; transition:background 0.2s; border:none; }
        .toggle.on { background:var(--green); }
        .toggle::after { content:''; position:absolute; width:18px; height:18px; background:white; border-radius:50%; top:3px; left:3px; transition:left 0.2s; box-shadow:0 1px 3px rgba(0,0,0,0.2); }
        .toggle.on::after { left:23px; }
        .toggle-label { font-size:13px; color:var(--text-secondary); }

        .modal-footer { display:flex; gap:10px; justify-content:flex-end; margin-top:20px; padding-top:16px; border-top:1px solid var(--border); }
        .btn-cancel { padding:10px 20px; background:none; border:1.5px solid var(--border); border-radius:var(--radius-sm); font-family:inherit; font-size:13px; font-weight:600; color:var(--text-secondary); cursor:pointer; }
        .btn-save { padding:10px 20px; background:var(--navy); border:none; border-radius:var(--radius-sm); font-family:inherit; font-size:13px; font-weight:600; color:white; cursor:pointer; display:flex; align-items:center; gap:6px; }
        .btn-save:hover { background:var(--navy-dark); }
        .btn-save svg { width:15px; height:15px; }

        /* SIDE WIDGETS */
        .side-card { background:white; border-radius:10px; padding:14px; border:1px solid var(--border); }
        .side-card-title { font-size:12px; font-weight:700; color:var(--navy); margin-bottom:10px; }
        .quota-bar { height:6px; background:var(--border); border-radius:99px; overflow:hidden; margin:8px 0; }
        .quota-fill { height:100%; background:var(--navy); border-radius:99px; }
        .quota-info { display:grid; grid-template-columns:1fr 1fr; gap:8px; margin-top:10px; }
        .quota-item { background:var(--bg); border-radius:8px; padding:8px; text-align:center; }
        .quota-item .q-label { font-size:10px; color:var(--text-muted); font-weight:600; }
        .quota-item .q-val { font-size:18px; font-weight:700; color:var(--navy); }
        .quota-note { font-size:11px; color:var(--text-secondary); margin-top:8px; line-height:1.5; display:flex; gap:6px; }

        .side-help { background:var(--navy); border-radius:10px; padding:16px; color:white; }
        .side-help h4 { font-size:13px; font-weight:700; margin-bottom:6px; }
        .side-help p { font-size:11px; color:rgba(255,255,255,0.7); line-height:1.5; margin-bottom:12px; }
        .btn-help { width:100%; padding:8px; background:white; border:none; border-radius:8px; font-family:inherit; font-size:12px; font-weight:700; color:var(--navy); cursor:pointer; display:flex; align-items:center; justify-content:center; gap:6px; }

        .recent-list { display:flex; flex-direction:column; gap:8px; }
        .recent-item { display:flex; align-items:center; gap:8px; }
        .recent-badge { width:28px; height:28px; border-radius:50%; background:var(--navy); color:white; display:flex; align-items:center; justify-content:center; font-size:11px; font-weight:700; flex-shrink:0; }
        .recent-name { font-size:12px; font-weight:600; color:var(--navy); }
        .recent-sub { font-size:11px; color:var(--text-muted); }

        /* CONFIRM */
        .confirm-modal { background:white; border-radius:20px; width:100%; max-width:400px; padding:32px; text-align:center; }
        .confirm-icon { width:60px; height:60px; border-radius:50%; background:var(--bg); display:flex; align-items:center; justify-content:center; margin:0 auto 16px; }
        .confirm-icon svg { width:26px; height:26px; color:var(--navy); }
        .confirm-title { font-size:18px; font-weight:700; color:var(--navy); margin-bottom:8px; }
        .confirm-text { font-size:13px; color:var(--text-secondary); line-height:1.6; margin-bottom:20px; }
        .confirm-btns { display:flex; gap:10px; }
        .btn-cc { flex:1; padding:11px; border:1.5px solid var(--border); border-radius:var(--radius-sm); background:white; font-family:inherit; font-size:13px; font-weight:600; color:var(--text-secondary); cursor:pointer; }
        .btn-co { flex:1; padding:11px; border:none; border-radius:var(--radius-sm); background:var(--navy); font-family:inherit; font-size:13px; font-weight:600; color:white; cursor:pointer; }
        .btn-co:hover { background:var(--navy-dark); }

        /* TOAST */
        .toast { position:fixed; bottom:24px; right:24px; padding:14px 20px; border-radius:12px; font-size:13px; font-weight:600; display:flex; align-items:center; gap:10px; box-shadow:var(--shadow-md); z-index:999; transform:translateY(80px); opacity:0; transition:all 0.3s; }
        .toast.show { transform:translateY(0); opacity:1; }
        .toast.success { background:#059669; color:white; }
        .toast.error   { background:var(--red); color:white; }
        .toast svg { width:16px; height:16px; }
    </style>
</head>
<body>

<aside class="sidebar">
    <div class="sidebar-brand"><h2>Satu Atap RW</h2><p>Manajemen Digital</p></div>
    <nav class="sidebar-nav">
        <a href="dashboard.php" class="nav-item"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>Dashboard</a>
        <a href="datawarga.php" class="nav-item"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>Data Warga</a>
        <a href="monitoring.php" class="nav-item"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>Monitoring</a>
        <a href="users.php" class="nav-item active"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="23" y1="11" x2="17" y2="11"/><line x1="20" y1="8" x2="20" y2="14"/></svg>Kelola User</a>
    </nav>
    <div class="sidebar-bottom">
        <a href="profilrw.php" class="nav-item">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>Profil Saya</a>
       <a href="#" class="nav-item" id="btnLogout" style="color:#dc2626"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>Keluar</a>
    </div>
</aside>

<div class="main">
    <header class="topbar">
        <div class="topbar-search">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input type="text" id="searchInput" placeholder="Cari akun RT atau username..." oninput="onSearch()">
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
                <div class="page-title">Kelola Akun RT</div>
                <div class="page-sub">Manajemen kredensial dan hak akses pengurus RT wilayah RW 08.</div>
            </div>
            <button class="btn-primary" onclick="openModal()">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="19" y1="8" x2="19" y2="14"/><line x1="22" y1="11" x2="16" y2="11"/></svg>
                Tambah Akun RT Baru
            </button>
        </div>

        <!-- STATS -->
        <div class="stat-grid">
            <div class="stat-card"><div class="stat-icon blue"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg></div><div><div class="stat-label">Total Akun</div><div class="stat-value" id="sTotal">—</div></div></div>
            <div class="stat-card"><div class="stat-icon green"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg></div><div><div class="stat-label">Aktif</div><div class="stat-value" id="sAktif">—</div></div></div>
            <div class="stat-card"><div class="stat-icon red"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg></div><div><div class="stat-label">Nonaktif</div><div class="stat-value" id="sNonaktif">—</div></div></div>
            <div class="stat-card"><div class="stat-icon gray"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg></div><div><div class="stat-label">Login Terakhir</div><div class="stat-value" style="font-size:14px;margin-top:4px" id="sLastLogin">—</div></div></div>
        </div>

        <!-- TABLE -->
        <div class="card">
            <div class="card-header">
                <div class="card-title">Daftar Akun Ketua RT</div>
                <div class="card-actions">
                    <button class="btn-outline"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg>Filter</button>
                    <button class="btn-outline"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>Ekspor</button>
                </div>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Nama Ketua RT</th>
                        <th>Wilayah RT</th>
                        <th>Status Akun</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    <tr><td colspan="5" style="text-align:center;padding:24px;color:var(--text-muted)">Memuat data...</td></tr>
                </tbody>
            </table>
            <div class="pagination">
                <span id="pagInfo">—</span>
                <div class="pag-btns" id="pagBtns"></div>
            </div>
        </div>

        <!-- INFO KEAMANAN -->
        <div class="info-card">
            <div class="info-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg></div>
            <div>
                <div class="info-title">Informasi Keamanan Akun</div>
                <div class="info-text">Hanya Admin RW yang memiliki wewenang untuk mereset password ketua RT. Pastikan Anda telah melakukan verifikasi identitas sebelum menyerahkan kredensial baru. Akses nonaktif akan segera memutus sesi pengguna yang sedang berjalan.</div>
            </div>
            <a href="#" class="info-link">Panduan Admin</a>
        </div>
    </div>
</div>

<!-- MODAL TAMBAH/EDIT -->
<div class="modal-overlay" id="modalOverlay">
    <div class="modal" style="max-width:800px;max-height:90vh">
        <div class="modal-form">
            <div class="modal-header">
                <div>
                    <div class="modal-title" id="modalTitle">Tambah Akun RT Baru</div>
                    <div class="modal-sub" id="modalSub">Daftarkan ketua RT baru ke dalam ekosistem Satu Atap RW.</div>
                </div>
                <button class="modal-close" onclick="closeModal()"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>
            </div>
            <input type="hidden" id="editId">

            <div class="section-title"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>Informasi Personal</div>
            <div class="form-grid">
                <div class="form-group full"><label class="form-label">Nama Lengkap Ketua RT *</label><input class="form-control" id="fNama" placeholder="Contoh: Bpk. Bambang Pamungkas"></div>
                <div class="form-group"><label class="form-label">Nomor WhatsApp</label><input class="form-control" id="fWA" placeholder="+62 812-3456-7890"></div>
                <div class="form-group"><label class="form-label">Alamat Email</label><input class="form-control" id="fEmail" placeholder="rt01@gmail.com" type="email"></div>
            </div>

            <div class="section-title"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>Wilayah Penugasan</div>
            <div class="form-grid">
                <div class="form-group"><label class="form-label">Nomor RT *</label>
                    <select class="form-control" id="fRT">
                        <option value="">Pilih RT</option>
                        <?php for($i=1;$i<=8;$i++) echo "<option value='$i'>RT 0$i</option>"; ?>
                    </select>
                </div>
                <div class="form-group"><label class="form-label">Wilayah RW</label>
                <input class="form-control" value="RW 08" disabled></div>
            </div>

            <div class="section-title"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>Kredensial Akun</div>
            <div class="form-grid">
                <div class="form-group"><label class="form-label">Username *</label><input class="form-control" id="fUsername" placeholder="ketuart01"></div>
               <div class="form-group">
    <label class="form-label">Password *</label>
    <div style="position:relative">
        <input class="form-control" id="fPassword" type="password" placeholder="••••••••" style="padding-right:40px">
        <button type="button" onclick="toggleEyePw('fPassword', this)" style="position:absolute;right:10px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:var(--text-muted);display:flex;align-items:center">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
        </button>
    </div>
</div>
            </div>

            <div style="margin-bottom:20px">
                <div class="section-title"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>Status Akun</div>
                <div class="toggle-wrap">
                    <button type="button" class="toggle on" id="toggleStatus" onclick="toggleStatus()"></button>
                    <span class="toggle-label" id="toggleLabel">Aktif — Akun langsung dapat digunakan setelah dibuat.</span>
                </div>
            </div>

            <div class="modal-footer">
                <button class="btn-cancel" onclick="closeModal()">Batal</button>
                <button class="btn-save" onclick="saveUser()">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                    Simpan Data RT
                </button>
            </div>
        </div>

        <!-- SIDE PANEL -->
        <div class="modal-side">
            <div class="side-card">
                <div class="side-card-title">Kuota Akun RT</div>
                <div style="display:flex;justify-content:space-between;font-size:12px;color:var(--text-muted)"><span>TERPAKAI</span><span id="quotaText">— / 8 RT</span></div>
                <div class="quota-bar"><div class="quota-fill" id="quotaFill" style="width:0%"></div></div>
                <div class="quota-info">
                    <div class="quota-item"><div class="q-label">SISA KUOTA</div><div class="q-val" id="quotaSisa">—</div></div>
                    <div class="quota-item"><div class="q-label">STATUS AKTIF</div><div class="q-val" id="quotaAktif">—</div></div>
                </div>
                <div class="quota-note"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink:0;margin-top:2px"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>Kuota RW Anda terbatas pada 8 RT sesuai wilayah.</div>
            </div>

            <div class="side-help">
                <h4>Bantuan?</h4>
                <p>Kesulitan saat mendaftarkan user baru? Hubungi tim technical support kami.</p>
                <button class="btn-help"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 13 19.79 19.79 0 0 1 1.61 4.35 2 2 0 0 1 3.6 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L7.91 9.91a16 16 0 0 0 6.16 6.16l.91-.91a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>Hubungi Support</button>
            </div>

            <div class="side-card">
                <div class="side-card-title">RT Terbaru Terdaftar</div>
                <div class="recent-list" id="recentList"></div>
            </div>
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
<div class="toast" id="toast"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg><span id="toastMsg"></span></div>

<script>
document.getElementById('btnLogout').addEventListener('click', function(e) {
    e.preventDefault();
    document.getElementById('logoutOverlay').style.display = 'flex';
});
document.getElementById('logoutOverlay').addEventListener('click', function(e) {
    if (e.target === this) this.style.display = 'none';
});

const rtColors = ['#1a2e5a','#059669','#7c3aed','#db2777','#d97706','#0891b2','#dc2626','#065f46'];
let statusAktif = true;
let searchTimeout;
let allUsers = [];

function toggleStatus() {
    statusAktif = !statusAktif;
    const btn = document.getElementById('toggleStatus');
    const lbl = document.getElementById('toggleLabel');
    btn.className = 'toggle' + (statusAktif ? ' on' : '');
    lbl.textContent = statusAktif ? 'Aktif — Akun langsung dapat digunakan setelah dibuat.' : 'Nonaktif — Akun tidak dapat login sampai diaktifkan.';
}

function toggleEyePw(id, btn) {
    const input = document.getElementById(id);
    const show = input.type === 'password';
    input.type = show ? 'text' : 'password';
    btn.innerHTML = show
        ? `<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="1" y1="1" x2="23" y2="23"/><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/></svg>`
        : `<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>`;
}

async function loadUsers() {
    const search = document.getElementById('searchInput').value;
    const res = await fetch('/backend/api/users.php?search=' + encodeURIComponent(search));
    const d = await res.json();
    if (!d.success) return;

    allUsers = d.data;

    // Stats
    document.getElementById('sTotal').textContent   = String(d.total).padStart(2,'0');
    document.getElementById('sAktif').textContent   = String(d.total_aktif).padStart(2,'0');
    document.getElementById('sNonaktif').textContent= String(d.total_nonaktif).padStart(2,'0');

    if (d.last_login) {
        const ll = new Date(d.last_login);
        const diff = Math.round((Date.now() - ll) / 60000);
        document.getElementById('sLastLogin').textContent = diff < 60 ? diff + ' Menit Lalu' : Math.round(diff/60) + ' Jam Lalu';
    }

    // Quota
    document.getElementById('quotaText').textContent = d.total_aktif + ' / 8 RT';
    document.getElementById('quotaFill').style.width = (d.total_aktif / 8 * 100) + '%';
    document.getElementById('quotaSisa').textContent = 8 - d.total;
    document.getElementById('quotaAktif').textContent = d.total_aktif;

    // Recent
    const recent = [...d.data].slice(-2).reverse();
    document.getElementById('recentList').innerHTML = recent.map(u => `
        <div class="recent-item">
            <div class="recent-badge" style="background:${rtColors[(u.nomor_rt-1)%8]}">0${u.nomor_rt}</div>
            <div><div class="recent-name">${u.nama_lengkap}</div><div class="recent-sub">RT ${String(u.nomor_rt).padStart(2,'0')}</div></div>
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="${u.status==='aktif'?'#059669':'#dc2626'}" stroke-width="2.5" style="margin-left:auto"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
        </div>
    `).join('');

    // Table
    if (!d.data.length) {
        document.getElementById('tableBody').innerHTML = '<tr><td colspan="5" style="text-align:center;padding:24px;color:var(--text-muted)">Tidak ada akun RT</td></tr>';
        document.getElementById('pagInfo').textContent = '0 akun RT';
        return;
    }

    document.getElementById('tableBody').innerHTML = d.data.map(u => `
        <tr>
            <td>
                <div class="user-cell">
                    <div class="user-avatar" style="background:${rtColors[(u.nomor_rt-1)%8]}">RT${String(u.nomor_rt).padStart(2,'0')}</div>
                    <span style="font-weight:600">${u.username}</span>
                </div>
            </td>
            <td>${u.nama_lengkap}</td>
            <td>Rukun Tetangga ${String(u.nomor_rt).padStart(2,'0')}</td>
            <td><span class="badge-${u.status}"><span class="badge-dot"></span>${u.status.charAt(0).toUpperCase()+u.status.slice(1)}</span></td>
            <td>
                <div class="action-btns">
                    <button class="btn-reset-pw" title="Reset Password" onclick="resetPassword(${u.id},'${u.nama_lengkap}')">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21.5 2v6h-6M2.5 22v-6h6M2 11.5a10 10 0 0 1 18.8-4.3M22 12.5a10 10 0 0 1-18.8 4.3"/></svg>
                    </button>
                    <button class="btn-edit" title="Edit" onclick="editUser(${JSON.stringify(u).replace(/"/g,'&quot;')})">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                    </button>
                </div>
            </td>
        </tr>
    `).join('');

    const total = d.data.length;
    document.getElementById('pagInfo').textContent = `Menampilkan ${total} dari ${total} akun RT`;
    document.getElementById('pagBtns').innerHTML = `
        <button class="pag-btn active">1</button>
        ${total > 5 ? '<button class="pag-btn">2</button>' : ''}
    `;
}

function onSearch() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(loadUsers, 400);
}

function openModal() {
    document.getElementById('editId').value = '';
    document.getElementById('modalTitle').textContent = 'Tambah Akun RT Baru';
    document.getElementById('modalSub').textContent = 'Daftarkan ketua RT baru ke dalam ekosistem Satu Atap RW.';
    document.getElementById('fUsername').readOnly = false;
    document.getElementById('fRT').disabled = false;
    ['fNama','fWA','fEmail','fUsername','fPassword'].forEach(id => document.getElementById(id).value = '');
    document.getElementById('fRT').value = '';
    statusAktif = true;
    document.getElementById('toggleStatus').className = 'toggle on';
    document.getElementById('toggleLabel').textContent = 'Aktif — Akun langsung dapat digunakan setelah dibuat.';
    document.getElementById('modalOverlay').classList.add('open');
}

function editUser(u) {
    document.getElementById('editId').value = u.id;
    document.getElementById('modalTitle').textContent = 'Edit Akun RT';
    document.getElementById('modalSub').textContent = 'Perbarui informasi akun ketua RT.';
    document.getElementById('fNama').value     = u.nama_lengkap;
    document.getElementById('fWA').value       = u.nomor_wa || '';
    document.getElementById('fEmail').value    = u.email || '';
    document.getElementById('fUsername').value = u.username;
    document.getElementById('fUsername').readOnly = false;
    document.getElementById('fRT').value       = u.nomor_rt;
    document.getElementById('fRT').disabled    = false;
    document.getElementById('fPassword').value = '';
    document.getElementById('fPassword').placeholder = 'Kosongkan jika tidak ingin ubah password';
    statusAktif = u.status === 'aktif';
    document.getElementById('toggleStatus').className = 'toggle' + (statusAktif ? ' on' : '');
    document.getElementById('toggleLabel').textContent = statusAktif ? 'Aktif — Akun langsung dapat digunakan.' : 'Nonaktif — Akun tidak dapat login.';
    document.getElementById('modalOverlay').classList.add('open');
}

function closeModal() {
    document.getElementById('modalOverlay').classList.remove('open');
}

async function saveUser() {
    const editId = document.getElementById('editId').value;
    const body = {
        nama_lengkap: document.getElementById('fNama').value,
        nomor_wa:     document.getElementById('fWA').value,
        email:        document.getElementById('fEmail').value,
        nomor_rt:     document.getElementById('fRT').value,
        username:     document.getElementById('fUsername').value,
        password:     document.getElementById('fPassword').value,
        status:       statusAktif ? 'aktif' : 'nonaktif',
    };
    if (editId) body.id = editId;

    if (!body.nama_lengkap || !body.nomor_rt || !body.username) {
        showToast('Nama, RT, dan Username wajib diisi', 'error'); return;
    }
    if (!editId && !body.password) {
        showToast('Password wajib diisi untuk akun baru', 'error'); return;
    }

    const method = editId ? 'PUT' : 'POST';
    const res = await fetch('/backend/api/users.php', { method, headers:{'Content-Type':'application/json'}, body:JSON.stringify(body) });
    const d = await res.json();

    if (d.success) {
        showToast(d.message, 'success');
        closeModal();
        loadUsers();
    } else {
        showToast(d.message || 'Gagal menyimpan', 'error');
    }
}

function resetPassword(id, nama) {
    const pw = prompt(`Reset password untuk ${nama}?\nMasukkan password baru:`);
    if (!pw) return;
    fetch('/backend/api/users.php', {
        method: 'PUT',
        headers: {'Content-Type':'application/json'},
        body: JSON.stringify({ id, password: pw, nama_lengkap: nama })
    }).then(r=>r.json()).then(d => {
        showToast(d.success ? 'Password berhasil direset' : d.message, d.success ? 'success' : 'error');
    });
}

function showToast(msg, type='success') {
    const t = document.getElementById('toast');
    t.className = 'toast ' + type;
    document.getElementById('toastMsg').textContent = msg;
    t.classList.add('show');
    setTimeout(() => t.classList.remove('show'), 3500);
}

loadUsers();
</script>
</body>
</html>