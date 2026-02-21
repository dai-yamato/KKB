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

        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
            font-size: 0.9rem;
        }
        .data-table th, .data-table td {
            text-align: left;
            padding: 0.75rem;
            border-bottom: 1px solid var(--border-color);
        }
        .data-table th {
            color: var(--text-muted);
            font-weight: 600;
        }
        .user-chip {
            display: inline-block;
            background: rgba(255, 255, 255, 0.1);
            padding: 2px 8px;
            border-radius: 6px;
            margin: 2px;
            font-size: 0.8rem;
            cursor: pointer;
            transition: background 0.2s;
        }
        .user-chip:hover {
            background: rgba(16, 185, 129, 0.2);
            color: #10b981;
        }

        /* Modal Styles */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(4px);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 2000;
        }
        .modal-container {
            background: var(--surface-color);
            padding: 2rem;
            border-radius: 16px;
            width: 90%;
            max-width: 400px;
            border: 1px solid var(--border-color);
            box-shadow: 0 10px 25px rgba(0,0,0,0.5);
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
        <!-- Households Table -->
        <div class="admin-card">
            <h2>登録家庭・ユーザー一覧</h2>
            <p style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 1rem;">
                システムに登録されているすべての家庭と、それに紐づくユーザー情報を確認できます。
            </p>
            <button id="reload-households-btn" class="btn-primary" style="padding: 0.5rem 1rem; font-size: 0.8rem;">🔄 再読み込み</button>
            <div style="overflow-x: auto; margin-top: 0.5rem;">
                <table class="data-table" id="households-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>家庭名</th>
                            <th>登録日</th>
                            <th>データ数 (件)</th>
                            <th>紐づくユーザー (名前 / 権限)</th>
                        </tr>
                    </thead>
                    <tbody id="households-tbody">
                        <tr><td colspan="5">読み込み中...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Contacts Table -->
        <div class="admin-card">
            <h2>お問い合わせ一覧</h2>
            <p style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 1rem;">
                ユーザーや未登録ゲストからのお問い合わせ内容を確認できます。
            </p>
            <button id="reload-contacts-btn" class="btn-primary" style="padding: 0.5rem 1rem; font-size: 0.8rem;">🔄 再読み込み</button>
            <div style="overflow-x: auto; margin-top: 0.5rem;">
                <table class="data-table" id="contacts-table">
                    <thead>
                        <tr>
                            <th>日時</th>
                            <th>送信者</th>
                            <th>内容</th>
                        </tr>
                    </thead>
                    <tbody id="contacts-tbody">
                        <tr><td colspan="3">読み込み中...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>

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

    <!-- User Detail Modal -->
    <div id="user-modal" class="modal-overlay" style="display: none;">
        <div class="modal-container">
            <h3 style="margin-top: 0; margin-bottom: 1rem; color: var(--primary-color);">ユーザー詳細</h3>
            <div id="modal-content-area" style="font-size: 0.95rem; line-height: 1.8; color: var(--text-main);">
                <!-- dynamic content -->
            </div>
            <div style="text-align: right; margin-top: 1.5rem;">
                <button class="logout-btn" onclick="closeUserModal()">閉じる</button>
            </div>
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

        // --- Households ---
        const fetchHouseholds = async () => {
            const tbody = document.getElementById('households-tbody');
            tbody.innerHTML = '<tr><td colspan="5">読み込み中...</td></tr>';
            try {
                const res = await fetch('/api/system-admin/households', { headers });
                if (res.status === 401 || res.status === 403) logoutAdmin();
                const households = await res.json();
                
                tbody.innerHTML = '';
                if (households.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="5" style="text-align: center; color: var(--text-muted);">登録がありません。</td></tr>';
                    return;
                }

                households.forEach(hh => {
                    const tr = document.createElement('tr');
                    
                    const dt = new Date(hh.created_at);
                    const formattedDate = `${dt.getFullYear()}/${String(dt.getMonth()+1).padStart(2,'0')}/${String(dt.getDate()).padStart(2,'0')} ${String(dt.getHours()).padStart(2,'0')}:${String(dt.getMinutes()).padStart(2,'0')}`;
                    
                    const usersHtml = hh.users.map(u => {
                        const userJson = JSON.stringify(u).replace(/"/g, '&quot;');
                        return `<span class="user-chip" onclick="openUserModal('${userJson}')">${u.name} <small style="color:inherit; opacity:0.7;">(${u.role === 'admin' ? '管理者' : '一般'})</small></span>`;
                    }).join('');

                    tr.innerHTML = `
                        <td>${hh.id}</td>
                        <td><strong>${hh.name}</strong></td>
                        <td><span style="color:var(--text-muted); font-size: 0.8rem;">${formattedDate}</span></td>
                        <td>
                            <span style="font-size: 0.8rem; color: var(--text-muted); line-height: 1.4; display: inline-block;">
                                取引: <strong style="color:var(--text-main);">${hh.transactions_count || 0}</strong><br>
                                カテゴリ: <strong style="color:var(--text-main);">${hh.categories_count || 0}</strong><br>
                                予算: <strong style="color:var(--text-main);">${hh.budgets_count || 0}</strong>
                            </span>
                        </td>
                        <td>${usersHtml || '<span style="color:var(--text-muted);">ユーザーなし</span>'}</td>
                    `;
                    tbody.appendChild(tr);
                });
            } catch (err) {
                tbody.innerHTML = `<tr><td colspan="5" style="color:#ef4444;">エラー: ${err.message}</td></tr>`;
            }
        };

        document.getElementById('reload-households-btn').addEventListener('click', fetchHouseholds);

        // --- Contacts ---
        const fetchContacts = async () => {
            const tbody = document.getElementById('contacts-tbody');
            tbody.innerHTML = '<tr><td colspan="3">読み込み中...</td></tr>';
            try {
                const res = await fetch('/api/system-admin/contacts', { headers });
                if (res.status === 401 || res.status === 403) logoutAdmin();
                const contacts = await res.json();
                
                tbody.innerHTML = '';
                if (contacts.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="3" style="text-align: center; color: var(--text-muted);">お問い合わせはありません。</td></tr>';
                    return;
                }

                contacts.forEach(c => {
                    const tr = document.createElement('tr');
                    const dt = new Date(c.created_at);
                    const formattedDate = `${dt.getFullYear()}/${String(dt.getMonth()+1).padStart(2,'0')}/${String(dt.getDate()).padStart(2,'0')} ${String(dt.getHours()).padStart(2,'0')}:${String(dt.getMinutes()).padStart(2,'0')}`;
                    
                    const senderHtml = c.user 
                        ? `<span style="color:var(--text-main); font-weight:700;">${Math.floor(c.user.id) === c.user_id ? c.user.name : c.name}</span><br><span style="font-size:0.8rem; color:var(--text-muted);">${c.email}</span><br><span class="user-chip" style="margin-left:0; margin-top:4px;">登録ユーザー (ID: ${c.user_id})</span>`
                        : `<span style="color:var(--text-main); font-weight:700;">${c.name}</span><br><span style="font-size:0.8rem; color:var(--text-muted);">${c.email}</span><br><span class="user-chip" style="margin-left:0; margin-top:4px; background: rgba(244,63,94,0.1); color: var(--accent-color);">ゲスト</span>`;

                    tr.innerHTML = `
                        <td style="white-space:nowrap; vertical-align:top;"><span style="color:var(--text-muted); font-size: 0.8rem;">${formattedDate}</span></td>
                        <td style="vertical-align:top; min-width: 150px;">${senderHtml}</td>
                        <td style="vertical-align:top; line-height: 1.6; word-break: break-all;">${c.message.replace(/\n/g, '<br>')}</td>
                    `;
                    tbody.appendChild(tr);
                });
            } catch (err) {
                tbody.innerHTML = `<tr><td colspan="3" style="color:#ef4444;">エラー: ${err.message}</td></tr>`;
            }
        };

        document.getElementById('reload-contacts-btn').addEventListener('click', fetchContacts);

        // --- Modal Logic ---
        const openUserModal = (userJsonStr) => {
            const u = JSON.parse(userJsonStr);
            const dt = new Date(u.created_at);
            const formattedDate = `${dt.getFullYear()}/${String(dt.getMonth()+1).padStart(2,'0')}/${String(dt.getDate()).padStart(2,'0')} ${String(dt.getHours()).padStart(2,'0')}:${String(dt.getMinutes()).padStart(2,'0')}`;
            
            const content = `
                <div><span style="color:var(--text-muted); display:inline-block; width: 80px;">ユーザーID:</span> <strong>${u.id}</strong></div>
                <div><span style="color:var(--text-muted); display:inline-block; width: 80px;">名前:</span> <strong style="color: white;">${u.name}</strong></div>
                <div><span style="color:var(--text-muted); display:inline-block; width: 80px;">メール:</span> <strong style="color: white;">${u.email}</strong></div>
                <div><span style="color:var(--text-muted); display:inline-block; width: 80px;">権限:</span> <strong style="color: ${u.role === 'admin' ? '#10b981' : 'white'};">${u.role === 'admin' ? '管理者' : '一般'}</strong></div>
                <div><span style="color:var(--text-muted); display:inline-block; width: 80px;">登録日時:</span> <strong>${formattedDate}</strong></div>
            `;
            document.getElementById('modal-content-area').innerHTML = content;
            document.getElementById('user-modal').style.display = 'flex';
        };

        const closeUserModal = () => {
            document.getElementById('user-modal').style.display = 'none';
        };

        // Fetch on load
        fetchCategories();
        fetchLogs();
        fetchHouseholds();
        fetchContacts();
    </script>
</body>
</html>
