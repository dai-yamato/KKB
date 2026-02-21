<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KKB Premium | ログイン</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;700&family=Noto+Sans+JP:wght@400;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <script src="{{ asset('js/kkb-settings.js') }}"></script>
    <style>
        .login-container {
            width: 100%;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--bg-color);
        }

        .login-card {
            background: var(--surface-color);
            padding: 3rem;
            border-radius: 32px;
            border: 1px solid var(--border-color);
            width: 100%;
            max-width: 420px;
            text-align: center;
        }

        .form-group {
            margin-bottom: 1.25rem;
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
            transition: border-color 0.2s;
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
            margin-top: 0.5rem;
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

        .error-alert {
            background: rgba(244, 63, 94, 0.1);
            border: 1px solid rgba(244, 63, 94, 0.3);
            color: var(--accent-color);
            padding: 0.75rem 1rem;
            border-radius: 10px;
            margin-bottom: 1.25rem;
            font-size: 0.875rem;
            display: none;
            text-align: left;
        }
    </style>
</head>

<body>
    <div class="login-container">
        <div class="login-card">
            <div style="font-size: 2.5rem; margin-bottom: 0.5rem;">💎</div>
            <h1 style="margin-bottom: 0.25rem; font-size: 1.75rem;">KKB Premium</h1>
            <p style="color: var(--text-muted); margin-bottom: 2rem; font-size: 0.9rem;">ログインして家計を管理しましょう</p>

            <div id="error-alert" class="error-alert"></div>

            <form id="login-form">
                @csrf
                <div class="form-group">
                    <label for="email">メールアドレス</label>
                    <input type="email" id="email" name="email" class="form-control"
                        placeholder="example@mail.com" required autocomplete="email">
                </div>

                <div class="form-group">
                    <label for="password">パスワード</label>
                    <input type="password" id="password" name="password" class="form-control"
                        placeholder="パスワードを入力" required autocomplete="current-password">
                </div>

                <button type="submit" class="submit-btn" id="submit-btn">ログイン</button>
            </form>

            <p style="text-align: center; margin-top: 2rem; font-size: 0.875rem; color: var(--text-muted);">
                アカウントをお持ちでないですか？
                <a href="/register" style="color: var(--primary-color); text-decoration: none; font-weight: 700;">新規登録</a>
            </p>
        </div>
    </div>

    <script>
        // Already logged in → redirect
        if (localStorage.getItem('kkb_householdId') && localStorage.getItem('kkb_userId')) {
            window.location.href = "{{ route('dashboard') }}";
        }

        document.getElementById('login-form').addEventListener('submit', async (e) => {
            e.preventDefault();
            const email    = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            const errorEl  = document.getElementById('error-alert');
            const btn      = document.getElementById('submit-btn');

            errorEl.style.display = 'none';
            btn.disabled = true;
            btn.textContent = 'ログイン中...';

            try {
                const response = await fetch('/api/login', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ email, password }),
                });

                const result = await response.json();

                if (response.ok) {
                    localStorage.setItem('kkb_householdId', result.household.id);
                    localStorage.setItem('kkb_userId',      result.user.id);
                    localStorage.setItem('kkb_currentUser', result.user.role);
                    localStorage.setItem('kkb_userName',    result.user.name);
                    window.location.href = "{{ route('dashboard') }}";
                } else {
                    errorEl.textContent  = result.message || 'ログインに失敗しました';
                    errorEl.style.display = 'block';
                }
            } catch (err) {
                errorEl.textContent  = 'ネットワークエラーが発生しました';
                errorEl.style.display = 'block';
            } finally {
                btn.disabled = false;
                btn.textContent = 'ログイン';
            }
        });
    </script>
</body>
</html>
