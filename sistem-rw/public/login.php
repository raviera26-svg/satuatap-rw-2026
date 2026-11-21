<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Satu Atap RW — Login</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --navy: #1a2e5a;
            --navy-dark: #0f1e3d;
            --navy-mid: #243a6e;
            --blue-soft: #e8edf8;
            --blue-lighter: #f0f3fb;
            --bg: #eef1f9;
            --white: #ffffff;
            --text-primary: #1a2e5a;
            --text-secondary: #5a6a8a;
            --text-muted: #8a9ab8;
            --border: #d4daea;
            --input-bg: #f7f9fd;
            --shadow-sm: 0 1px 3px rgba(26,46,90,0.08);
            --shadow-md: 0 4px 20px rgba(26,46,90,0.12);
            --shadow-lg: 0 8px 40px rgba(26,46,90,0.15);
            --radius: 12px;
            --radius-sm: 8px;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: var(--bg);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 24px;
            background-image: radial-gradient(ellipse at 20% 50%, rgba(26,46,90,0.04) 0%, transparent 60%),
                              radial-gradient(ellipse at 80% 20%, rgba(26,46,90,0.04) 0%, transparent 60%);
        }

        .brand {
            text-align: center;
            margin-bottom: 28px;
        }

        .brand-icon {
            width: 64px;
            height: 64px;
            background: var(--white);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 16px;
            box-shadow: var(--shadow-md);
        }

        .brand-icon svg {
            width: 32px;
            height: 32px;
            color: var(--navy);
        }

        .brand h1 {
            font-size: 24px;
            font-weight: 700;
            color: var(--navy);
            letter-spacing: -0.3px;
        }

        .brand p {
            font-size: 13px;
            color: var(--text-secondary);
            margin-top: 4px;
        }

        .card {
            background: var(--white);
            border-radius: 20px;
            padding: 36px;
            width: 100%;
            max-width: 440px;
            box-shadow: var(--shadow-lg);
            border: 1px solid rgba(26,46,90,0.06);
        }

        .card-header {
            margin-bottom: 28px;
        }

        .card-header h2 {
            font-size: 20px;
            font-weight: 700;
            color: var(--text-primary);
        }

        .card-header p {
            font-size: 11px;
            font-weight: 600;
            color: var(--text-muted);
            letter-spacing: 0.8px;
            text-transform: uppercase;
            margin-top: 4px;
        }

        .form-group {
            margin-bottom: 18px;
        }

        .form-label {
            display: block;
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 0.7px;
            text-transform: uppercase;
            color: var(--text-secondary);
            margin-bottom: 8px;
        }

        .input-wrap {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            width: 18px;
            height: 18px;
            pointer-events: none;
        }

        .form-control {
            width: 100%;
            height: 48px;
            padding: 0 44px 0 44px;
            background: var(--input-bg);
            border: 1.5px solid var(--border);
            border-radius: var(--radius-sm);
            font-family: inherit;
            font-size: 14px;
            color: var(--text-primary);
            transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
            outline: none;
        }

        .form-control::placeholder { color: var(--text-muted); }

        .form-control:focus {
            border-color: var(--navy);
            background: var(--white);
            box-shadow: 0 0 0 3px rgba(26,46,90,0.08);
        }

        select.form-control {
            appearance: none;
            cursor: pointer;
        }

        .select-arrow {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            pointer-events: none;
        }

        .password-toggle {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: var(--text-muted);
            padding: 4px;
            display: flex;
            align-items: center;
            transition: color 0.2s;
        }
        .password-toggle:hover { color: var(--navy); }

        .form-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 24px;
        }

        .remember-wrap {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
        }

        .remember-wrap input[type="checkbox"] {
            width: 16px;
            height: 16px;
            accent-color: var(--navy);
            cursor: pointer;
        }

        .remember-wrap span {
            font-size: 13px;
            color: var(--text-secondary);
        }

        .forgot-link {
            font-size: 13px;
            font-weight: 600;
            color: var(--navy);
            text-decoration: none;
        }
        .forgot-link:hover { text-decoration: underline; }

        .btn-login {
            width: 100%;
            height: 52px;
            background: var(--navy);
            color: white;
            border: none;
            border-radius: var(--radius-sm);
            font-family: inherit;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: background 0.2s, transform 0.1s, box-shadow 0.2s;
            letter-spacing: 0.2px;
        }

        .btn-login:hover {
            background: var(--navy-dark);
            box-shadow: 0 4px 16px rgba(26,46,90,0.3);
        }

        .btn-login:active { transform: scale(0.99); }

        .btn-login.loading {
            pointer-events: none;
            opacity: 0.8;
        }

        .divider {
            height: 1px;
            background: var(--border);
            margin: 24px 0;
        }

        .help-text {
            text-align: center;
            font-size: 13px;
            color: var(--text-secondary);
        }

        .help-text a {
            color: var(--navy);
            font-weight: 600;
            text-decoration: none;
        }
        .help-text a:hover { text-decoration: underline; }

        .footer-bottom {
            margin-top: 24px;
            text-align: center;
        }

        .footer-bottom p {
            font-size: 12px;
            color: var(--text-muted);
            margin-bottom: 8px;
        }

        .footer-links {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 16px;
        }

        .footer-links a {
            font-size: 12px;
            color: var(--text-muted);
            text-decoration: none;
        }
        .footer-links a:hover { color: var(--navy); }

        .dot { color: var(--border); }

        .alert {
            padding: 12px 16px;
            border-radius: var(--radius-sm);
            font-size: 13px;
            margin-bottom: 18px;
            display: none;
        }

        .alert-error {
            background: #fef2f2;
            color: #c0392b;
            border: 1px solid #fecaca;
        }

        .spinner {
            width: 18px;
            height: 18px;
            border: 2px solid rgba(255,255,255,0.4);
            border-top-color: white;
            border-radius: 50%;
            animation: spin 0.7s linear infinite;
            display: none;
        }

        @keyframes spin { to { transform: rotate(360deg); } }
    </style>
</head>
<body>

    <div class="brand">
        <div class="brand-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                <polyline points="9 22 9 12 15 12 15 22"/>
            </svg>
        </div>
        <h1>Satu Atap RW</h1>
        <p>Manajemen Digital Lingkungan Terpadu</p>
    </div>

    <div class="card">
        <div class="card-header">
            <h2>Masuk ke Akun</h2>
            <p>Silakan masukkan kredensial anda</p>
        </div>

        <div class="alert alert-error" id="alertError"></div>

        <!-- Username -->
        <div class="form-group">
            <label class="form-label">Username</label>
            <div class="input-wrap">
                <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                    <circle cx="12" cy="7" r="4"/>
                </svg>
                <input type="text" class="form-control" id="username" placeholder="Masukkan username" autocomplete="username">
            </div>
        </div>

        <!-- Password -->
        <div class="form-group">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px;">
                <label class="form-label" style="margin:0">Kata Sandi</label>
                <a href="#" class="forgot-link">Lupa sandi?</a>
            </div>
            <div class="input-wrap">
                <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                    <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                </svg>
                <input type="password" class="form-control" id="password" placeholder="••••••••" autocomplete="current-password">
                <button type="button" class="password-toggle" id="togglePassword">
                    <svg id="eyeIcon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                        <circle cx="12" cy="12" r="3"/>
                    </svg>
                </button>
            </div>
        </div>

        <div class="form-footer">
            <label class="remember-wrap">
                <input type="checkbox" id="remember">
                <span>Ingat saya di perangkat ini</span>
            </label>
        </div>

        <button class="btn-login" id="btnLogin" onclick="doLogin()">
            <span id="btnText">Masuk</span>
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" id="btnArrow">
                <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/>
                <polyline points="10 17 15 12 10 7"/>
                <line x1="15" y1="12" x2="3" y2="12"/>
            </svg>
            <div class="spinner" id="spinner"></div>
        </button>

        <div class="divider"></div>

        <p class="help-text">Butuh bantuan akses? <a href="#">Hubungi IT RW</a></p>
    </div>

    <div class="footer-bottom">
        <p>Satu Atap RW v2.4.0</p>
        <div class="footer-links">
            <a href="#">Kebijakan Privasi</a>
            <span class="dot">•</span>
            <a href="#">Syarat &amp; Ketentuan</a>
        </div>
    </div>

    <script>

        // Toggle password visibility
        document.getElementById('togglePassword').addEventListener('click', function() {
            const input = document.getElementById('password');
            const isPassword = input.type === 'password';
            input.type = isPassword ? 'text' : 'password';
            document.getElementById('eyeIcon').innerHTML = isPassword
                ? `<line x1="1" y1="1" x2="23" y2="23"/><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/>`
                : `<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>`;
        });

        // Enter key
        document.getElementById('password').addEventListener('keydown', function(e) {
            if (e.key === 'Enter') doLogin();
        });

        function showError(msg) {
            const el = document.getElementById('alertError');
            el.textContent = msg;
            el.style.display = 'block';
        }

        function hideError() {
            document.getElementById('alertError').style.display = 'none';
        }

        function setLoading(loading) {
            const btn = document.getElementById('btnLogin');
            const text = document.getElementById('btnText');
            const arrow = document.getElementById('btnArrow');
            const spinner = document.getElementById('spinner');
            btn.classList.toggle('loading', loading);
            text.textContent = loading ? 'Memproses...' : 'Masuk';
            arrow.style.display = loading ? 'none' : 'block';
            spinner.style.display = loading ? 'block' : 'none';
        }

        async function doLogin() {
    hideError();
    const username = document.getElementById('username').value.trim();
    const password = document.getElementById('password').value;
    const remember = document.getElementById('remember').checked;

    if (!username || !password) {
        showError('Username dan password wajib diisi.');
        return;
    }

    if (!remember) {
        showError('Silakan centang "Ingat saya di perangkat ini" terlebih dahulu.');
        return;
    }
            setLoading(true);

            try {
                const res = await fetch('/backend/api/login.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ username, password })
                });
                const data = await res.json();

                if (data.success) {
                    window.location.href = data.redirect;
                } else {
                    showError(data.message || 'Login gagal.');
                    setLoading(false);
                }
            } catch (e) {
                showError('Terjadi kesalahan. Coba lagi.');
                setLoading(false);
            }
        }
    </script>
</body>
</html>
