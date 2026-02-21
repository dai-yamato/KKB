<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KKB Premium | <span id="page-title-text">新規登録</span></title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;700&family=Noto+Sans+JP:wght@400;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <script src="{{ asset('js/kkb-settings.js') }}"></script>
    <style>
        .register-container {
            width: 100%;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--bg-color);
            padding: 2rem;
        }

        .register-card {
            background: var(--surface-color);
            padding: 3rem;
            border-radius: 32px;
            border: 1px solid var(--border-color);
            width: 100%;
            max-width: 500px;
        }

        .form-group {
            margin-bottom: 1.5rem;
            text-align: left;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 700;
            color: var(--text-muted);
            font-size: 0.875rem;
        }

        .form-control {
            width: 100%;
            padding: 1rem;
            border: 1px solid var(--border-color);
            border-radius: 12px;
            background: var(--bg-color);
            color: var(--text-main);
            font-family: inherit;
            font-size: 1rem;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.2);
        }

        .submit-btn {
            width: 100%;
            padding: 1rem;
            background: var(--primary-gradient);
            color: white;
            border: none;
            border-radius: 12px;
            font-weight: 700;
            font-size: 1rem;
            cursor: pointer;
            margin-top: 1rem;
            transition: all 0.3s ease;
        }

        .submit-btn:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: var(--shadow-strong);
        }

        .submit-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .error-msg {
            color: var(--accent-color);
            font-size: 0.75rem;
            margin-top: 0.25rem;
            display: none;
        }

        .invite-badge {
            background: rgba(16, 185, 129, 0.1);
            border: 1px solid rgba(16, 185, 129, 0.3);
            color: var(--primary-color);
            padding: 0.75rem 1rem;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            font-size: 0.875rem;
            text-align: left;
            display: none;
        }
    </style>
</head>

<body>
    <div class="register-container">
        <div class="register-card">
            <div style="font-size: 2.5rem; text-align: center; margin-bottom: 0.5rem;">💎</div>
            <h1 id="page-title" style="margin-bottom: 0.5rem; text-align: center;">新規アカウント登録</h1>
            <p id="page-subtitle" style="color: var(--text-muted); margin-bottom: 2rem; text-align: center;">
                家計簿を共有する新しい家庭を作成します</p>

            <!-- Invite Banner (shown when ?invite= param present) -->
            <div id="invite-badge" class="invite-badge">
                🏠 <strong id="invite-household-name"></strong> に招待されています
            </div>

            <form id="register-form">
                @csrf

                <!-- Household name field (hidden when using invite link) -->
                <div class="form-group" id="household-name-group">
                    <label for="household_name">家計名（例：山田家）</label>
                    <input type="text" id="household_name" name="household_name" class="form-control"
                        placeholder="家計名を入力">
                    <div id="error-household_name" class="error-msg"></div>
                </div>

                <div class="form-group">
                    <label for="name">あなたのお名前</label>
                    <input type="text" id="name" name="name" class="form-control" placeholder="お名前を入力" required>
                    <div id="error-name" class="error-msg"></div>
                </div>



                <div class="form-group">
                    <label for="email">メールアドレス</label>
                    <input type="email" id="email" name="email" class="form-control"
                        placeholder="example@mail.com" required>
                    <div id="error-email" class="error-msg"></div>
                </div>

                <div class="form-group">
                    <label for="password">パスワード</label>
                    <input type="password" id="password" name="password" class="form-control"
                        placeholder="8文字以上" required>
                    <div id="error-password" class="error-msg"></div>
                </div>

                <div class="form-group">
                    <label for="password_confirmation">パスワード（確認用）</label>
                    <input type="password" id="password_confirmation" name="password_confirmation"
                        class="form-control" placeholder="再度入力" required>
                </div>

                <button type="submit" class="submit-btn" id="submit-btn">登録して開始する</button>

                <p style="text-align: center; margin-top: 2rem; font-size: 0.875rem; color: var(--text-muted);">
                    既にアカウントをお持ちですか？
                    <a href="/login" style="color: var(--primary-color); text-decoration: none; font-weight: 700;">ログイン</a>
                </p>
            </form>
        </div>
    </div>

    <script>
        const params      = new URLSearchParams(window.location.search);
        const inviteToken = params.get('invite');
        let isInviteMode  = false;

        // ---- Invite mode setup ----
        if (inviteToken) {
            (async () => {
                try {
                    const res  = await fetch(`/api/invitations/${inviteToken}`);
                    const data = await res.json();

                    if (!res.ok) {
                        document.getElementById('page-subtitle').textContent = data.message || '招待リンクが無効です';
                        document.getElementById('submit-btn').disabled = true;
                        return;
                    }

                    isInviteMode = true;

                    // Update UI for invite mode
                    document.getElementById('page-title').textContent       = '招待を承認する';
                    document.getElementById('page-subtitle').textContent    = `${data.household_name} に参加します`;
                    document.getElementById('invite-household-name').textContent = data.household_name;
                    document.getElementById('invite-badge').style.display   = 'block';
                    document.getElementById('household-name-group').style.display = 'none';
                    document.getElementById('household_name').required       = false;
                    document.getElementById('submit-btn').textContent        = '参加する';

                } catch (e) {
                    document.getElementById('page-subtitle').textContent = 'エラーが発生しました';
                }
            })();
        } else {
            // Normal registration: household_name is required
            document.getElementById('household_name').required = true;
        }

        // ---- Form submission ----
        document.getElementById('register-form').addEventListener('submit', async (e) => {
            e.preventDefault();

            const formData = new FormData(e.target);
            const data     = Object.fromEntries(formData.entries());
            const btn      = document.getElementById('submit-btn');

            // Reset errors
            document.querySelectorAll('.error-msg').forEach(el => {
                el.style.display = 'none';
                el.textContent   = '';
            });

            btn.disabled    = true;
            btn.textContent = '処理中...';

            try {
                let url, body;

                if (isInviteMode) {
                    // Join existing household via invite token
                    url  = `/api/invitations/${inviteToken}/accept`;
                    body = {
                        name:                  data.name,
                        email:                 data.email,
                        password:              data.password,
                        password_confirmation: data.password_confirmation,
                    };
                } else {
                    // Create new household
                    url  = '/register';
                    body = data;
                }

                const response = await fetch(url, {
                    method:  'POST',
                    headers: {
                        'Content-Type':  'application/json',
                        'X-CSRF-TOKEN':  document.querySelector('input[name="_token"]').value,
                        'Accept':        'application/json',
                    },
                    body: JSON.stringify(body),
                });

                const result = await response.json();

                if (response.ok) {
                    localStorage.setItem('kkb_householdId', result.household.id);
                    localStorage.setItem('kkb_userId',      result.user.id);
                    localStorage.setItem('kkb_currentUser', result.user.role);
                    localStorage.setItem('kkb_userName',    result.user.name);
                    window.location.href = "{{ route('dashboard') }}";
                } else if (response.status === 422) {
                    for (const [key, messages] of Object.entries(result.errors || {})) {
                        const el = document.getElementById(`error-${key}`);
                        if (el) { el.textContent = messages[0]; el.style.display = 'block'; }
                    }
                    // Show generic message if no field-level error
                    if (result.message && !result.errors) {
                        alert(result.message);
                    }
                } else {
                    alert(result.message || '登録に失敗しました。');
                }
            } catch (err) {
                alert('ネットワークエラーが発生しました。');
            } finally {
                btn.disabled    = false;
                btn.textContent = isInviteMode ? '参加する' : '登録して開始する';
            }
        });
    </script>
</body>
</html>
