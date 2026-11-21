<?php
require_once __DIR__ . '/../../backend/middleware/auth.php';
requireRT();
$user  = getCurrentUser();
$rtPad = str_pad($user['nomor_rt'], 2, '0', STR_PAD_LEFT);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil — Panel RT</title>
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
        .sidebar { width:var(--sidebar-w); background:var(--white); border-right:1px solid var(--border); display:flex; flex-direction:column; position:fixed; top:0;left:0;bottom:0; z-index:100; }
        .sidebar-brand { padding: 24px 20px 20px; border-bottom: 1px solid var(--border); display:flex; align-items:center; gap:10px; }
        .sidebar-brand-icon { width:38px; height:38px; background:var(--green); border-radius:10px; display:flex; align-items:center; justify-content:center; color:white; flex-shrink:0; }
        .sidebar-brand-icon svg { width:20px; height:20px; }
        .sidebar-brand h2 { font-size:16px; font-weight:700; color:var(--navy); }
        .sidebar-brand p { font-size:11px; color:var(--text-muted); margin-top:1px; }
        .sidebar-nav { flex:1; padding:16px 12px; display:flex; flex-direction:column; gap:2px; }
        .nav-item { display:flex; align-items:center; gap:10px; padding:10px 12px; border-radius:var(--radius-sm); text-decoration:none; color:var(--text-secondary); font-size:14px; font-weight:500; transition:all 0.15s; }
        .nav-item:hover { background:var(--bg); color:var(--navy); }
        .nav-item.active { background:var(--green-light); color:var(--green-dark); font-weight:600; }
        .nav-item svg { width:18px; height:18px; flex-shrink:0; }
        .sidebar-bottom { padding:16px 12px; border-top:1px solid var(--border); display:flex; flex-direction:column; gap:2px; }

        /* MAIN */
        .main { margin-left:var(--sidebar-w); flex:1; display:flex; flex-direction:column; }
        .topbar { background:var(--white); border-bottom:1px solid var(--border); padding:0 28px; height:60px; display:flex; align-items:center; gap:16px; position:sticky; top:0; z-index:99; }
        .topbar-right { display:flex; align-items:center; gap:12px; margin-left:auto; }
        .icon-btn { width:36px; height:36px; border-radius:50%; background:var(--bg); border:1px solid var(--border); display:flex; align-items:center; justify-content:center; cursor:pointer; color:var(--text-secondary); }
        .icon-btn:hover { background:var(--green); color:white; border-color:var(--green); }
        .icon-btn svg { width:16px; height:16px; }
        .user-info { display:flex; align-items:center; gap:10px; }
        .user-info-text { text-align:right; }
        .user-info-text .name { font-size:13px; font-weight:600; color:var(--navy); }
        .user-info-text .role { font-size:11px; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.5px; }
        .avatar-lg { width:80px; height:80px; border-radius:50%; background:var(--green); color:white; display:flex; align-items:center; justify-content:center; font-weight:700; font-size:28px; flex-shrink:0; }
        .avatar-sm { width:36px; height:36px; border-radius:50%; background:var(--green); color:white; display:flex; align-items:center; justify-content:center; font-weight:700; font-size:13px; }

        /* CONTENT */
        .content { padding:28px; flex:1; max-width:900px; }
        .page-title { font-size:22px; font-weight:700; color:var(--navy); margin-bottom:4px; }
        .page-sub { font-size:13px; color:var(--text-muted); margin-bottom:24px; }

        /* PROFILE HEADER CARD */
        .profile-header {
            background: linear-gradient(135deg, var(--green-dark) 0%, var(--green) 100%);
            border-radius: var(--radius);
            padding: 28px;
            display: flex;
            align-items: center;
            gap: 24px;
            margin-bottom: 20px;
            position: relative;
            overflow: hidden;
        }
        .profile-header::before {
            content: '';
            position: absolute;
            top: -40px; right: -40px;
            width: 200px; height: 200px;
            border-radius: 50%;
            background: rgba(255,255,255,0.08);
        }
        .profile-header::after {
            content: '';
            position: absolute;
            bottom: -60px; right: 60px;
            width: 160px; height: 160px;
            border-radius: 50%;
            background: rgba(255,255,255,0.06);
        }
        .profile-avatar-wrap { position: relative; z-index: 1; }
        .profile-info { flex: 1; z-index: 1; }
        .profile-info h2 { font-size: 22px; font-weight: 700; color: white; }
        .profile-info .role-badge {
            display: inline-flex; align-items: center; gap: 6px;
            background: rgba(255,255,255,0.18); color: white;
            padding: 4px 12px; border-radius: 99px;
            font-size: 12px; font-weight: 600;
            margin-top: 6px;
        }
        .profile-info .join-date { font-size: 12px; color: rgba(255,255,255,0.7); margin-top: 8px; }
        .profile-stats { display: flex; gap: 24px; z-index: 1; }
        .profile-stat { text-align: center; }
        .profile-stat .val { font-size: 24px; font-weight: 700; color: white; }
        .profile-stat .lbl { font-size: 11px; color: rgba(255,255,255,0.7); margin-top: 2px; }

        /* TWO COL LAYOUT */
        .two-col { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }

        /* CARD */
        .card { background: var(--white); border-radius: var(--radius); padding: 24px; box-shadow: var(--shadow-sm); border: 1px solid var(--border); margin-bottom: 16px; }
        .card-title { font-size: 15px; font-weight: 700; color: var(--green-dark); margin-bottom: 4px; display: flex; align-items: center; gap: 8px; }
        .card-title svg { width: 17px; height: 17px; }
        .card-sub { font-size: 12px; color: var(--text-muted); margin-bottom: 20px; }

        /* FORM */
        .form-group { margin-bottom: 16px; }
        .form-label { display: block; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; color: var(--text-secondary); margin-bottom: 6px; }
        .form-control { width: 100%; height: 42px; padding: 0 14px; background: var(--bg); border: 1.5px solid var(--border); border-radius: 8px; font-family: inherit; font-size: 13px; color: var(--text-primary); outline: none; transition: border-color 0.2s, background 0.2s; }
        .form-control:focus { border-color: var(--green); background: white; }
        .form-control:disabled { opacity: 0.5; cursor: not-allowed; }
        .input-wrap { position: relative; }
        .input-wrap .form-control { padding-right: 44px; }
        .input-wrap .eye-btn { position: absolute; right: 12px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: var(--text-muted); display: flex; align-items: center; }
        .input-wrap .eye-btn:hover { color: var(--green-dark); }
        .input-wrap .eye-btn svg { width: 17px; height: 17px; }

        .form-hint { font-size: 11px; color: var(--text-muted); margin-top: 4px; }

        /* BUTTONS */
        .btn-primary { width: 100%; height: 44px; background: var(--green); border: none; border-radius: 8px; font-family: inherit; font-size: 14px; font-weight: 600; color: white; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; transition: background 0.15s; }
        .btn-primary:hover { background: var(--green-dark); }
        .btn-primary svg { width: 16px; height: 16px; }

        /* INFO ROW */
        .info-row { display: flex; align-items: center; gap: 12px; padding: 12px 0; border-bottom: 1px solid var(--border); }
        .info-row:last-child { border-bottom: none; padding-bottom: 0; }
        .info-row:first-child { padding-top: 0; }
        .info-icon { width: 36px; height: 36px; border-radius: 8px; background: var(--bg); display: flex; align-items: center; justify-content: center; flex-shrink: 0; color: var(--text-muted); }
        .info-icon svg { width: 16px; height: 16px; }
        .info-label { font-size: 11px; color: var(--text-muted); font-weight: 600; text-transform: uppercase; letter-spacing: 0.4px; }
        .info-value { font-size: 14px; color: var(--text-primary); font-weight: 500; margin-top: 1px; }

        .badge-aktif { display:inline-flex; align-items:center; gap:5px; padding:3px 10px; border-radius:99px; font-size:11px; font-weight:600; background:var(--green-light); color:var(--green-dark); }
        .badge-rt   { display:inline-flex; align-items:center; gap:5px; padding:3px 10px; border-radius:99px; font-size:11px; font-weight:600; background:var(--green-light); color:var(--green-dark); }

        /* PASSWORD STRENGTH */
        .strength-bar { display: flex; gap: 4px; margin-top: 6px; }
        .strength-seg { flex: 1; height: 3px; border-radius: 99px; background: var(--border); transition: background 0.3s; }
        .strength-seg.weak   { background: var(--red); }
        .strength-seg.medium { background: var(--amber); }
        .strength-seg.strong { background: var(--green); }
        .strength-text { font-size: 11px; margin-top: 4px; font-weight: 600; }
        .strength-text.weak   { color: var(--red); }
        .strength-text.medium { color: var(--amber); }
        .strength-text.strong { color: var(--green-dark); }

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
        <a href="dashboard.php" class="nav-item">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
            Dashboard
        </a>
        <a href="data-warga.php" class="nav-item">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            Data Warga
        </a>
    </nav>
    <div class="sidebar-bottom">
        <a href="profilrt.php" class="nav-item active">
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
        <div style="font-size:16px;font-weight:700;color:var(--navy)">Profil Saya</div>
        <div class="topbar-right">
            <div class="icon-btn"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg></div>
            <div class="user-info">
                <div class="user-info-text">
                    <div class="name" id="topbarName"><?= htmlspecialchars($user['nama']) ?></div>
                    <div class="role">Ketua RT <?= $rtPad ?></div>
                </div>
                <div class="avatar-sm" id="topbarAvatar"><?= strtoupper(substr($user['nama'],0,2)) ?></div>
            </div>
        </div>
    </header>

    <div class="content">
        <div class="page-title">Profil Akun</div>
        <div class="page-sub">Kelola informasi pribadi dan keamanan akun Anda.</div>

        <!-- PROFILE HEADER -->
        <div class="profile-header">
            <div class="profile-avatar-wrap">
                <div class="avatar-lg" id="profileAvatar"><?= strtoupper(substr($user['nama'],0,2)) ?></div>
            </div>
            <div class="profile-info">
                <h2 id="profileName"><?= htmlspecialchars($user['nama']) ?></h2>
                <div class="role-badge">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                    Ketua RT <?= $rtPad ?>
                </div>
                <div class="join-date" id="joinDate">Bergabung sejak —</div>
            </div>
            <div class="profile-stats">
                <div class="profile-stat">
                    <div class="val" id="statTotalWarga">—</div>
                    <div class="lbl">Total Warga</div>
                </div>
                <div class="profile-stat">
                    <div class="val" id="statTotalKK">—</div>
                    <div class="lbl">Kepala Keluarga</div>
                </div>
                <div class="profile-stat">
                    <div class="val">RT <?= $rtPad ?></div>
                    <div class="lbl">Wilayah</div>
                </div>
            </div>
        </div>

        <div class="two-col">
            <!-- KIRI: Info Akun + Edit Profil -->
            <div>
                <!-- Info Akun -->
                <div class="card">
                    <div class="card-title">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                        Informasi Akun
                    </div>
                    <div class="card-sub">Detail akun yang terdaftar di sistem.</div>

                    <div class="info-row">
                        <div class="info-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg></div>
                        <div><div class="info-label">Username</div><div class="info-value" id="infoUsername">—</div></div>
                    </div>
                    <div class="info-row">
                        <div class="info-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg></div>
                        <div><div class="info-label">Role</div><div class="info-value"><span class="badge-rt">Ketua RT <?= $rtPad ?></span></div></div>
                    </div>
                    <div class="info-row">
                        <div class="info-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg></div>
                        <div><div class="info-label">Status</div><div class="info-value"><span class="badge-aktif">● Aktif</span></div></div>
                    </div>
                    <div class="info-row">
                        <div class="info-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg></div>
                        <div><div class="info-label">Terdaftar Sejak</div><div class="info-value" id="infoCreated">—</div></div>
                    </div>
                </div>

                <!-- Edit Profil -->
                <div class="card">
                    <div class="card-title">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                        Edit Profil
                    </div>
                    <div class="card-sub">Perbarui informasi pribadi Anda.</div>

                    <div class="form-group">
                        <label class="form-label">Nama Lengkap</label>
                        <input class="form-control" id="fNama" placeholder="Nama lengkap">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Nomor WhatsApp</label>
                        <input class="form-control" id="fWA" placeholder="+62 812-xxxx-xxxx">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Alamat Email</label>
                        <input class="form-control" id="fEmail" type="email" placeholder="email@contoh.com">
                    </div>
                    <button class="btn-primary" onclick="saveProfil()">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                        Simpan Perubahan
                    </button>
                </div>
            </div>

            <!-- KANAN: Ganti Password -->
            <div>
                <div class="card">
                    <div class="card-title">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                        Ganti Password
                    </div>
                    <div class="card-sub">Pastikan password baru minimal 6 karakter dan mudah diingat.</div>

                    <div class="form-group">
                        <label class="form-label">Password Lama</label>
                        <div class="input-wrap">
                            <input class="form-control" id="fPwLama" type="password" placeholder="Masukkan password lama">
                            <button class="eye-btn" type="button" onclick="togglePw('fPwLama', this)">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            </button>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Password Baru</label>
                        <div class="input-wrap">
                            <input class="form-control" id="fPwBaru" type="password" placeholder="Minimal 6 karakter" oninput="checkStrength(this.value)">
                            <button class="eye-btn" type="button" onclick="togglePw('fPwBaru', this)">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            </button>
                        </div>
                        <div class="strength-bar">
                            <div class="strength-seg" id="seg1"></div>
                            <div class="strength-seg" id="seg2"></div>
                            <div class="strength-seg" id="seg3"></div>
                        </div>
                        <div class="strength-text" id="strengthText"></div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Konfirmasi Password Baru</label>
                        <div class="input-wrap">
                            <input class="form-control" id="fPwKonfirmasi" type="password" placeholder="Ulangi password baru">
                            <button class="eye-btn" type="button" onclick="togglePw('fPwKonfirmasi', this)">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            </button>
                        </div>
                        <div class="form-hint" id="matchHint"></div>
                    </div>

                    <button class="btn-primary" onclick="savePassword()">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                        Perbarui Password
                    </button>
                </div>

                <!-- Tips Keamanan -->
                <div class="card" style="background:linear-gradient(135deg,#f0fdf4,#dcfce7)">
                    <div class="card-title">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                        Tips Keamanan
                    </div>
                    <div style="display:flex;flex-direction:column;gap:10px;margin-top:4px">
                        <div style="display:flex;gap:10px;align-items:flex-start">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--green-dark)" stroke-width="2.5" style="flex-shrink:0;margin-top:2px"><polyline points="20 6 9 17 4 12"/></svg>
                            <span style="font-size:13px;color:var(--text-secondary)">Gunakan minimal 8 karakter</span>
                        </div>
                        <div style="display:flex;gap:10px;align-items:flex-start">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--green-dark)" stroke-width="2.5" style="flex-shrink:0;margin-top:2px"><polyline points="20 6 9 17 4 12"/></svg>
                            <span style="font-size:13px;color:var(--text-secondary)">Kombinasikan huruf, angka, dan simbol</span>
                        </div>
                        <div style="display:flex;gap:10px;align-items:flex-start">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--green-dark)" stroke-width="2.5" style="flex-shrink:0;margin-top:2px"><polyline points="20 6 9 17 4 12"/></svg>
                            <span style="font-size:13px;color:var(--text-secondary)">Jangan gunakan tanggal lahir sebagai password</span>
                        </div>
                        <div style="display:flex;gap:10px;align-items:flex-start">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--green-dark)" stroke-width="2.5" style="flex-shrink:0;margin-top:2px"><polyline points="20 6 9 17 4 12"/></svg>
                            <span style="font-size:13px;color:var(--text-secondary)">Ganti password secara berkala</span>
                        </div>
                    </div>
                </div>
            </div>
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
<div class="toast" id="toast"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg><span id="toastMsg"></span></div>

<script>
document.getElementById('btnLogout').addEventListener('click', function(e) {
    e.preventDefault();
    document.getElementById('logoutOverlay').style.display = 'flex';
});
document.getElementById('logoutOverlay').addEventListener('click', function(e) {
    if (e.target === this) this.style.display = 'none';
});

const BULAN = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];

function formatTanggal(str) {
    if (!str) return '—';
    const d = new Date(str);
    return d.getDate() + ' ' + BULAN[d.getMonth()] + ' ' + d.getFullYear();
}

async function loadProfil() {
    const res = await fetch('/backend/api/profilrw.php');
    const d   = await res.json();
    if (!d.success) return;
    const p = d.data;

    document.getElementById('profileName').textContent  = p.nama_lengkap;
    document.getElementById('profileAvatar').textContent = p.nama_lengkap.split(' ').slice(0,2).map(w=>w[0]).join('').toUpperCase();
    document.getElementById('topbarName').textContent   = p.nama_lengkap;
    document.getElementById('topbarAvatar').textContent = p.nama_lengkap.split(' ').slice(0,2).map(w=>w[0]).join('').toUpperCase();
    document.getElementById('joinDate').textContent     = 'Bergabung sejak ' + formatTanggal(p.created_at);

    document.getElementById('infoUsername').textContent  = p.username;
    document.getElementById('infoCreated').textContent   = formatTanggal(p.created_at);

    document.getElementById('fNama').value  = p.nama_lengkap;
    document.getElementById('fWA').value    = p.nomor_wa || '';
    document.getElementById('fEmail').value = p.email || '';

    // Stat warga RT ini
    const wRes = await fetch('/backend/api/warga.php?limit=1');
    const wD   = await wRes.json();
    if (wD.success) {
        document.getElementById('statTotalWarga').textContent = wD.stats.total_warga.toLocaleString('id-ID');
        document.getElementById('statTotalKK').textContent     = wD.stats.total_kk;
    }
}

async function saveProfil() {
    const body = {
        aksi: 'profil',
        nama_lengkap: document.getElementById('fNama').value.trim(),
        nomor_wa:     document.getElementById('fWA').value.trim(),
        email:        document.getElementById('fEmail').value.trim(),
    };
    if (!body.nama_lengkap) { showToast('Nama lengkap wajib diisi', 'error'); return; }

    const res = await fetch('/backend/api/profilrw.php', { method:'PUT', headers:{'Content-Type':'application/json'}, body:JSON.stringify(body) });
    const d   = await res.json();
    if (d.success) { showToast(d.message, 'success'); loadProfil(); }
    else showToast(d.message, 'error');
}

async function savePassword() {
    const lama       = document.getElementById('fPwLama').value;
    const baru       = document.getElementById('fPwBaru').value;
    const konfirmasi = document.getElementById('fPwKonfirmasi').value;

    if (!lama || !baru || !konfirmasi) { showToast('Semua field password wajib diisi', 'error'); return; }
    if (baru !== konfirmasi)           { showToast('Konfirmasi password tidak cocok', 'error'); return; }
    if (baru.length < 6)               { showToast('Password baru minimal 6 karakter', 'error'); return; }

    const res = await fetch('/backend/api/profilrw.php', {
        method: 'PUT',
        headers: {'Content-Type':'application/json'},
        body: JSON.stringify({ aksi:'password', password_lama:lama, password_baru:baru, password_konfirmasi:konfirmasi })
    });
    const d = await res.json();
    if (d.success) {
        showToast(d.message, 'success');
        document.getElementById('fPwLama').value = '';
        document.getElementById('fPwBaru').value = '';
        document.getElementById('fPwKonfirmasi').value = '';
        ['seg1','seg2','seg3'].forEach(id => document.getElementById(id).className = 'strength-seg');
        document.getElementById('strengthText').textContent = '';
    } else {
        showToast(d.message, 'error');
    }
}

function togglePw(id, btn) {
    const input = document.getElementById(id);
    const show  = input.type === 'password';
    input.type  = show ? 'text' : 'password';
    btn.innerHTML = show
        ? `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="1" y1="1" x2="23" y2="23"/><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/></svg>`
        : `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>`;
}

function checkStrength(val) {
    const segs = ['seg1','seg2','seg3'];
    const hint = document.getElementById('strengthText');
    segs.forEach(s => document.getElementById(s).className = 'strength-seg');
    hint.textContent = '';
    if (!val) return;

    let score = 0;
    if (val.length >= 6)  score++;
    if (val.length >= 10) score++;
    if (/[A-Z]/.test(val) && /[0-9]/.test(val)) score++;

    const levels = [
        { cls:'weak',   label:'Lemah' },
        { cls:'medium', label:'Sedang' },
        { cls:'strong', label:'Kuat' },
    ];
    for (let i = 0; i < score; i++) {
        document.getElementById(segs[i]).classList.add(levels[score-1].cls);
    }
    hint.textContent = levels[score-1]?.label || '';
    hint.className = 'strength-text ' + (levels[score-1]?.cls || '');
}

document.getElementById('fPwKonfirmasi').addEventListener('input', function() {
    const baru = document.getElementById('fPwBaru').value;
    const hint = document.getElementById('matchHint');
    if (!this.value) { hint.textContent = ''; return; }
    if (this.value === baru) {
        hint.textContent = '✓ Password cocok';
        hint.style.color = 'var(--green-dark)';
    } else {
        hint.textContent = '✗ Password tidak cocok';
        hint.style.color = 'var(--red)';
    }
});

function showToast(msg, type='success') {
    const t = document.getElementById('toast');
    t.className = 'toast ' + type;
    document.getElementById('toastMsg').textContent = msg;
    t.classList.add('show');
    setTimeout(() => t.classList.remove('show'), 3500);
}

loadProfil();
</script>
</body>
</html>