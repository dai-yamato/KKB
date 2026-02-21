<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>システム管理者ログイン | KKB</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-color: #0f172a;
            --surface-color: #1e293b;
            --primary-color: #f43f5e; /* Red for admin */
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --border-color: #334155;
        }

        body {
            font-family: 'Outfit', sans-serif;
            background: var(--bg-color);
            color: var(--text-main);
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
        }

        .login-card {
            background: var(--surface-color);
            padding: 3rem;
            border-radius: 20px;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.4);
            text-align: center;
            border: 1px solid var(--border-color);
        }

        input {
            width: 100%;
            padding: 1rem;
            margin-bottom: 1.5rem;
            background: var(--bg-color);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            color: var(--text-main);
            box-sizing: border-box;
        }

        input:focus {
            outline: none;
            border-color: var(--primary-color);
        }

        button {
            width: 100%;
            padding: 1rem;
            background: var(--primary-color);
            color: white;
            border: none;
            border-radius: 12px;
            font-weight: 700;
            cursor: pointer;
            font-size: 1.1rem;
            transition: opacity 0.2s;
        }

        button:hover {
            opacity: 0.9;
        }

        .error {
            color: #ef4444;
            margin-bottom: 1rem;
            font-size: 0.9rem;
            display: none;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <h2 style="margin-bottom: 0.5rem; color: var(--primary-color);">🛠️ システム管理</h2>
        <p style="color: var(--text-muted); margin-bottom: 2rem;">Authorized Personnel Only</p>
        
        <div id="error-msg" class="error"></div>

        <form id="admin-login-form">
            <input type="email" id="email" placeholder="メールアドレス" required>
            <input type="password" id="password" placeholder="パスワード" required>
            <button type="submit">ログイン</button>
        </form>
        <a href="/" style="display:block; margin-top:2rem; color: var(--text-muted); text-decoration:none; font-size: 0.8rem;">&larr; サービスTOPへ戻る</a>
    </div>

    <script>
        document.getElementById('admin-login-form').addEventListener('submit', async (e) => {
            e.preventDefault();
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            const errEl = document.getElementById('error-msg');

            errEl.style.display = 'none';

            try {
                const res = await fetch('/api/system-admin/login', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ email, password })
                });

                if (res.ok) {
                    const data = await res.json();
                    localStorage.setItem('kkb_sadminId', data.admin.id);
                    window.location.href = '/system-admin';
                } else {
                    const data = await res.json();
                    errEl.textContent = data.message || 'ログイン失敗';
                    errEl.style.display = 'block';
                }
            } catch (err) {
                errEl.textContent = 'ネットワークエラー';
                errEl.style.display = 'block';
            }
        });
    </script>
</body>
</html>
