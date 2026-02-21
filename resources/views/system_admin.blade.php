<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KKB Premium | システム管理画面</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-color: #0f172a;
            --surface-color: #1e293b;
            --primary-color: #f43f5e;
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --border-color: #334155;
        }

        body {
            font-family: 'Outfit', sans-serif;
            background: var(--bg-color);
            color: var(--text-main);
            margin: 0;
            padding: 0;
        }

        header {
            background: var(--surface-color);
            padding: 1rem 5%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid var(--border-color);
        }

        .header-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--primary-color);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .container {
            max-width: 1000px;
            margin: 2rem auto;
            padding: 0 5%;
        }

        .admin-card {
            background: var(--surface-color);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
        }

        .admin-card h2 {
            font-size: 1.25rem;
            font-weight: 700;
            margin-top: 0;
            margin-bottom: 0.5rem;
            color: var(--primary-color);
        }

        .log-viewer {
            background: #000;
            color: #10b981;
            padding: 1rem;
            border-radius: 8px;
            font-family: 'Courier New', Courier, monospace;
            font-size: 0.85rem;
            height: 400px;
            overflow-y: scroll;
            white-space: pre-wrap;
            word-break: break-all;
            margin-top: 1rem;
        }

        .textarea-json {
            width: 100%;
            height: 250px;
            background: #000;
            color: #10b981;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 1rem;
            font-family: 'Courier New', Courier, monospace;
            font-size: 0.9rem;
            margin-bottom: 1rem;
            resize: vertical;
            box-sizing: border-box;
        }

        .btn-primary {
            background: var(--primary-color);
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: opacity 0.2s;
        }

        .btn-primary:hover {
            opacity: 0.8;
        }

        .logout-btn {
            background: transparent;
            color: var(--text-muted);
            border: 1px solid var(--border-color);
            padding: 0.5rem 1rem;
            border-radius: 8px;
            cursor: pointer;
        }
    </style>
</head>
<body>

    <header>
        <div class="header-title"><span>🛠️</span> KKB Premium - システム管理</div>
        <div>
            <span style="margin-right: 1rem; font-size: 0.9rem;" id="admin-name"></span>
            <button class="logout-btn" onclick="logoutAdmin()">ログアウト</button>
        </div>
    </header>

    <div class="container">
        <div class="admin-card">
            <h2>デフォルトカテゴリ設定</h2>
            <p style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 1rem;">
                新規家庭作成時に登録されるシステム定義のカテゴリ（JSON形式）を編集します。
            </p>
            <textarea id="categories-json" class="textarea-json">読み込み中...</textarea>
            <button id="save-categories-btn" class="btn-primary">保存する</button>
            <div id="save-message" style="margin-top: 1rem; font-size: 0.9rem; font-weight: 600;"></div>
        </div>

        <div class="admin-card">
            <h2>システムログ (storage/logs/laravel.log)</h2>
            <p style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 1rem;">
                アプリのエラー調査等にご利用ください。
            </p>
            <button id="reload-logs-btn" class="btn-primary" style="padding: 0.5rem 1rem; font-size: 0.8rem;">🔄 再読み込み</button>
            <div id="log-viewer" class="log-viewer">読み込み中...</div>
        </div>
    </div>

    <script>
        const adminId = localStorage.getItem('kkb_sadminId');

        if (!adminId) {
            window.location.href = '/system-admin/login';
        }

        const headers = {
            'Content-Type': 'application/json',
            'X-SystemAdmin-Id': adminId
        };

        const logoutAdmin = () => {
            localStorage.removeItem('kkb_sadminId');
            window.location.href = '/system-admin/login';
        };

        // --- Categories ---
        const fetchCategories = async () => {
            try {
                const res = await fetch('/api/system-admin/categories', { headers });
                if (res.status === 401 || res.status === 403) logoutAdmin();
                const data = await res.json();
                document.getElementById('categories-json').value = JSON.stringify(data, null, 4);
            } catch (err) {
                document.getElementById('categories-json').value = '取得エラー: ' + err.message;
            }
        };

        document.getElementById('save-categories-btn').addEventListener('click', async () => {
            const btn = document.getElementById('save-categories-btn');
            const msg = document.getElementById('save-message');
            btn.disabled = true;
            msg.textContent = '';
            msg.style.color = 'inherit';

            try {
                const parsed = JSON.parse(document.getElementById('categories-json').value);
                const res = await fetch('/api/system-admin/categories', {
                    method: 'PUT',
                    headers: headers,
                    body: JSON.stringify({ categories: parsed })
                });

                if (!res.ok) throw new Error('保存エラー');

                msg.textContent = '保存しました。';
                msg.style.color = '#10b981';
                setTimeout(() => msg.textContent = '', 3000);
            } catch (err) {
                msg.textContent = 'JSON形式が不正かサーバーエラーです';
                msg.style.color = '#ef4444';
            } finally {
                btn.disabled = false;
            }
        });

        // --- Logs ---
        const fetchLogs = async () => {
            const viewer = document.getElementById('log-viewer');
            viewer.textContent = '読み込み中...';
            try {
                const res = await fetch('/api/system-admin/logs', { headers });
                if (res.status === 401 || res.status === 403) logoutAdmin();
                const data = await res.json();
                viewer.textContent = data.logs;
                viewer.scrollTop = viewer.scrollHeight;
            } catch (err) {
                viewer.textContent = 'ログ取得エラー: ' + err.message;
            }
        };

        document.getElementById('reload-logs-btn').addEventListener('click', fetchLogs);

        fetchCategories();
        fetchLogs();
    </script>
</body>
</html>
