@extends('layouts.app')

@section('title', 'KKB Premium | 設定')
@section('header_title', '設定 ⚙️')
@section('header_subtitle', 'アプリケーションをカスタマイズしましょう')

@section('content')
<div class="card" style="max-width: 600px;">
    <h2 style="margin-bottom: 2rem; font-size: 1.25rem;">基本設定</h2>

    <!-- Household Name -->
    <div class="form-group">
        <label>家計の名前</label>
        <div style="display: flex; gap: 1rem;">
            <input type="text" id="household-name-input" style="flex: 1; padding: 0.75rem; border-radius: 8px; border: 1px solid var(--border-color); background: var(--surface-color); color: var(--text-main);">
            <button id="update-household-btn" style="padding: 0.75rem 1.5rem; background: var(--primary-color); color: white; border: none; border-radius: 8px; font-weight: 700; cursor: pointer;">保存</button>
        </div>
    </div>

    <!-- Dark Mode -->
    <div class="form-group">
        <label>ダークモード</label>
        <div style="display: flex; align-items: center; gap: 1rem;">
            <span id="theme-label" style="font-size: 0.875rem; color: var(--text-muted);">ダーク</span>
            <div id="theme-toggle"
                style="width: 52px; height: 28px; border-radius: 14px; position: relative; cursor: pointer; transition: background 0.3s ease;"
                onclick="toggleThemeUI()">
                <div id="theme-knob"
                    style="width: 22px; height: 22px; border-radius: 50%; background: white; position: absolute; top: 3px; transition: left 0.3s ease; box-shadow: 0 1px 4px rgba(0,0,0,0.3);">
                </div>
            </div>
        </div>
    </div>



    <!-- Month Start Day -->
    <div class="form-group">
        <label>月の開始日</label>
        <div style="display: flex; align-items: center; gap: 1rem;">
            <input type="number" id="month-start-day" value="1" min="1" max="28"
                style="width: 80px; padding: 0.75rem; border-radius: 8px; border: 1px solid var(--border-color); background: var(--surface-color); color: var(--text-main); text-align: center;">
            <span style="font-size: 0.875rem; color: var(--text-muted);">日（例: 25日に設定すると毎月25日〜翌24日が1ヶ月）</span>
        </div>
    </div>

    <button id="save-basic-settings-btn" style="padding: 0.75rem 2rem; background: var(--primary-gradient); color: white; border: none; border-radius: 10px; font-weight: 700; cursor: pointer; font-family: inherit;">
        ✓ 基本設定を保存
    </button>

    <h2 style="margin-bottom: 1rem; font-size: 1.25rem;">家族メンバーの管理</h2>
    <div id="user-list" style="display: flex; flex-direction: column; gap: 0.75rem; margin-bottom: 1.5rem;">
        <!-- Loaded via JS -->
    </div>

    <!-- Invite Link Generator -->
    <div style="background: var(--bg-color); padding: 1.5rem; border-radius: 16px;">
        <h3 style="font-size: 0.875rem; margin-bottom: 0.5rem; color: var(--text-muted);">家族を招待する</h3>
        <p style="font-size: 0.8rem; color: var(--text-muted); margin-bottom: 1rem;">招待リンクを発行してメンバーに共有してください。リンクは7日間有効です（1家計に1つ）。</p>

        <button id="generate-invite-btn" style="padding: 0.75rem 1.5rem; background: var(--primary-color); color: white; border: none; border-radius: 8px; font-weight: 700; cursor: pointer; width: 100%;">
            🔗 招待リンクを発行する
        </button>

        <div id="invite-result" style="display: none; margin-top: 1rem;">
            <label style="font-size: 0.75rem; color: var(--text-muted); font-weight: 700; display: block; margin-bottom: 0.5rem;">招待URL（コピーして共有）</label>
            <div style="display: flex; gap: 0.5rem;">
                <input id="invite-url-input" type="text" readonly
                    style="flex: 1; padding: 0.75rem; border-radius: 8px; border: 1px solid var(--border-color); background: var(--surface-color); color: var(--text-main); font-size: 0.8rem;">
                <button id="copy-invite-btn" style="padding: 0.75rem 1rem; background: var(--surface-color); border: 1px solid var(--border-color); border-radius: 8px; cursor: pointer; color: var(--text-main);">コピー</button>
            </div>
            <p id="invite-expires" style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.5rem;"></p>
        </div>
    </div>

    <hr style="border: 0; height: 1px; background: var(--border-color); margin: 2rem 0;">

    <h2 style="margin-bottom: 2rem; font-size: 1.25rem; color: var(--accent-color);">データの管理</h2>
    <div style="display: flex; flex-direction: column; gap: 1rem;">
        <button id="export-btn" class="nav-item"
            style="width: 100%; border: 1px solid var(--border-color); justify-content: center; background: transparent;">JSON形式でエクスポート</button>
        <button id="reset-btn" class="nav-item"
            style="width: 100%; border: 1px solid var(--accent-color); color: var(--accent-color); justify-content: center; background: transparent;">全てのデータをリセット</button>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const householdId = localStorage.getItem('kkb_householdId');
    if (!householdId) {
        window.location.href = '/login';
    }

    const headers = {
        'Content-Type': 'application/json',
        'X-Household-Id': householdId
    };

    // ── Init UI from saved settings ───────────────────────────────────────
    function updateThemeToggleUI() {
        const isDark   = KKB.get('theme') === 'dark';
        const toggle   = document.getElementById('theme-toggle');
        const knob     = document.getElementById('theme-knob');
        const label    = document.getElementById('theme-label');
        if (!toggle) return;
        toggle.style.background = isDark ? 'var(--primary-color)' : '#cbd5e1';
        knob.style.left = isDark ? '27px' : '3px';
        label.textContent = isDark ? 'ダーク' : 'ライト';
    }

    window.toggleThemeUI = () => {
        KKB.toggleTheme();
        updateThemeToggleUI();
    };

    // Load saved values into controls
    document.getElementById('month-start-day').value   = KKB.get('monthStartDay');
    updateThemeToggleUI();

    // Save basic settings (month start day)
    document.getElementById('save-basic-settings-btn').addEventListener('click', () => {
        const monthStartDay = parseInt(document.getElementById('month-start-day').value, 10);

        if (monthStartDay < 1 || monthStartDay > 28) {
            alert('月の開始日は1〜28の間で入力してください。');
            return;
        }

        KKB.set('monthStartDay', monthStartDay);

        const btn = document.getElementById('save-basic-settings-btn');
        const old = btn.textContent;
        btn.textContent = '✅ 保存しました！';
        setTimeout(() => btn.textContent = old, 2000);
    });



    const fetchUsers = async () => {
        try {
            const res = await fetch(`/api/households/${householdId}/users`, { headers });
            const users = await res.json();
            const list = document.getElementById('user-list');
            const currentUserRole = localStorage.getItem('kkb_currentUser') || 'editor';
            const roleNames = { admin: '管理者', editor: '入力者' };
            const roleIcons = { admin: '👑', editor: '✍️' };

            list.innerHTML = users.map(u => {
                const roleSelect = currentUserRole === 'admin' ? `
                    <select onchange="updateUserRole(${u.id}, this.value)" style="padding: 0.25rem 0.5rem; border-radius: 6px; border: 1px solid var(--border-color); background: var(--surface-color); color: var(--text-main); font-size: 0.75rem; margin-top: 0.25rem;">
                        <option value="admin" ${u.role === 'admin' ? 'selected' : ''}>管理者</option>
                        <option value="editor" ${u.role === 'editor' ? 'selected' : ''}>入力者</option>
                    </select>
                ` : `<div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.25rem;">${roleNames[u.role] || u.role || 'メンバー'}</div>`;

                const deleteBtn = currentUserRole === 'admin' ? `
                    <button onclick="deleteUser(${u.id})" style="padding: 0.5rem; background: transparent; border: none; color: var(--accent-color); cursor: pointer; font-size: 0.875rem;">🗑️ 削除</button>
                ` : '';

                return `
                <div style="display: flex; align-items: center; justify-content: space-between; padding: 1rem; background: var(--bg-color); border-radius: 12px;">
                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                        <div style="width: 32px; height: 32px; border-radius: 50%; background: var(--primary-color); display: flex; align-items: center; justify-content: center; font-size: 0.875rem; color: white;">
                            ${roleIcons[u.role] || '👤'}
                        </div>
                        <div>
                            <div style="font-weight: 700;">${u.name}</div>
                            ${roleSelect}
                        </div>
                    </div>
                    ${deleteBtn}
                </div>
                `;
            }).join('');
        } catch (error) {
            console.error('Error fetching users:', error);
        }
    };

    window.updateUserRole = async (id, newRole) => {
        try {
            const res = await fetch(`/api/users/${id}/role`, { 
                method: 'PUT', 
                headers, 
                body: JSON.stringify({ role: newRole }) 
            });
            if (res.ok) {
                // If the user changed their own role, update local storage
                const currentUserId = localStorage.getItem('kkb_userId');
                if (String(id) === String(currentUserId)) {
                    localStorage.setItem('kkb_currentUser', newRole);
                    // Refresh page to apply updated permissions
                    window.location.reload();
                    return;
                }
                fetchUsers();
            } else {
                const err = await res.json();
                alert(err.message || '権限の更新に失敗しました');
                fetchUsers(); // reset select
            }
        } catch (error) {
            console.error('Error updating role:', error);
            fetchUsers(); // reset select
        }
    };

    window.deleteUser = async (id) => {
        if (!confirm('このユーザーを削除しますか？')) return;
        try {
            const res = await fetch(`/api/users/${id}`, { method: 'DELETE', headers });
            if (res.ok) fetchUsers();
            else {
                const err = await res.json();
                alert(err.message || '削除に失敗しました');
            }
        } catch (error) {
            console.error('Error deleting user:', error);
        }
    };

    // --- Invite link generator ---
    document.getElementById('generate-invite-btn').addEventListener('click', async () => {
        const btn = document.getElementById('generate-invite-btn');
        btn.disabled    = true;
        btn.textContent = '発行中...';
        try {
            const res = await fetch('/api/invitations/generate', {
                method:  'POST',
                headers,
            });
            const data = await res.json();
            if (res.ok) {
                document.getElementById('invite-url-input').value  = data.url;
                document.getElementById('invite-expires').textContent = `有効期限: ${new Date(data.expires_at).toLocaleString('ja-JP')}`;
                document.getElementById('invite-result').style.display = 'block';
            } else {
                alert(data.message || 'エラーが発生しました');
            }
        } catch (err) {
            alert('エラーが発生しました');
        } finally {
            btn.disabled    = false;
            btn.textContent = '🔗 招待リンクを発行する';
        }
    });

    document.getElementById('copy-invite-btn').addEventListener('click', () => {
        const input = document.getElementById('invite-url-input');
        input.select();
        document.execCommand('copy');
        const btn = document.getElementById('copy-invite-btn');
        const old = btn.textContent;
        btn.textContent = '✅ コピー完了';
        setTimeout(() => btn.textContent = old, 2000);
    });



    const fetchHousehold = async () => {
        try {
            const res = await fetch('/api/households', { headers });
            const households = await res.json();
            const current = households.find(h => String(h.id) === String(householdId));
            if (current) {
                document.getElementById('household-name-input').value = current.name;
            }
        } catch (error) {
            console.error('Error fetching household:', error);
        }
    };

    document.getElementById('update-household-btn').addEventListener('click', async () => {
        const name = document.getElementById('household-name-input').value;
        if (!name) return;
        try {
            const res = await fetch('/api/households', {
                method: 'PUT',
                headers,
                body: JSON.stringify({ name })
            });
            if (res.ok) alert('家計の名前を更新しました');
        } catch (error) {
            console.error('Error updating household:', error);
        }
    });

    fetchHousehold();
    fetchUsers();

    document.getElementById('export-btn').addEventListener('click', async () => {
        try {
            const [catRes, txRes, budgetRes] = await Promise.all([
                fetch('/api/categories', { headers }),
                fetch('/api/transactions', { headers }),
                fetch('/api/budgets?all=1', { headers })
            ]);
            const data = {
                categories: await catRes.json(),
                transactions: await txRes.json(),
                budgets: await budgetRes.json(),
                exportedAt: new Date().toISOString()
            };
            const blob = new Blob([JSON.stringify(data, null, 2)], { type: 'application/json' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `kkb_backup_${new Date().toISOString().slice(0, 10)}.json`;
            a.click();
            URL.revokeObjectURL(url);
        } catch (error) {
            console.error('Error exporting data:', error);
            alert('エクスポートに失敗しました');
        }
    });

    document.getElementById('reset-btn').addEventListener('click', async () => {
        if (confirm('全てのデータを削除しますか？この操作は取り消せません。')) {
            try {
                const response = await fetch('/api/reset', { 
                    method: 'POST',
                    headers: headers
                });
                if (response.ok) {
                    window.location.href = "{{ route('dashboard') }}";
                }
            } catch (error) {
                console.error('Error resetting system:', error);
                alert('リセットに失敗しました');
            }
        }
    });

    // Logout button
    const logoutBtn = document.createElement('button');
    logoutBtn.className = 'nav-item';
    logoutBtn.style.cssText = 'width: 100%; border: 1px solid var(--accent-color); color: var(--accent-color); justify-content: center; background: transparent; margin-top: 1rem;';
    logoutBtn.textContent = '🚪 ログアウト';
    logoutBtn.addEventListener('click', () => {
        if (confirm('ログアウトしますか？')) {
            localStorage.clear();
            window.location.href = '/login';
        }
    });
    document.getElementById('reset-btn').parentElement.appendChild(logoutBtn);
</script>
@endpush
