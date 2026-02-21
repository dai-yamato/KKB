<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'KKB Premium | 家計簿管理')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700;800&family=Noto+Sans+JP:wght@400;500;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <!-- Load settings early to apply theme before paint (prevents FOUC) -->
    <script src="{{ asset('js/kkb-settings.js') }}"></script>
    <script src="{{ asset('js/app.js') }}" defer></script>
    @stack('styles')
</head>

<body>
    <div class="app-container">
        <!-- Sidebar Navigation -->
        <aside class="sidebar">
            <div class="logo">
                <span>💎</span> KKB Premium
            </div>
            <nav>
                <ul class="nav-links">
                    <li><a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}"><span>📊</span> ダッシュボード</a></li>
                    <li><a href="{{ route('list') }}" class="nav-item {{ request()->routeIs('list') ? 'active' : '' }}"><span>📝</span> 履歴一覧</a></li>
                    <li><a href="{{ route('input') }}" class="nav-item {{ request()->routeIs('input') ? 'active' : '' }}"><span>✍️</span> 連続入力</a></li>
                    <li><a href="{{ route('budget') }}" class="nav-item {{ request()->routeIs('budget') ? 'active' : '' }}"><span>💰</span> 予算管理</a></li>
                    <li><a href="{{ route('comparison') }}" class="nav-item {{ request()->routeIs('comparison') ? 'active' : '' }}"><span>⚖️</span> 前月比較</a></li>
                    <li><a href="{{ route('stats') }}" class="nav-item {{ request()->routeIs('stats') ? 'active' : '' }}"><span>📈</span> 統計・レポート</a></li>
                    <li><a href="{{ route('calendar') }}" class="nav-item {{ request()->routeIs('calendar') ? 'active' : '' }}"><span>📅</span> カレンダー</a></li>
                    <li><a href="{{ route('categories') }}" class="nav-item {{ request()->routeIs('categories') ? 'active' : '' }}"><span>🏷️</span> カテゴリ管理</a></li>
                    <li><a href="{{ route('settings') }}" class="nav-item {{ request()->routeIs('settings') ? 'active' : '' }}"><span>⚙️</span> 設定</a></li>
                </ul>
            </nav>
        </aside>

        <!-- Main Content Area -->
        <main class="main-content">
            <header>
                <div class="header-title">
                    <h1>@yield('header_title', 'こんにちは！✨')</h1>
                    <p>@yield('header_subtitle', '今日の収支を記録しましょう')</p>
                </div>
                <div class="user-profile" id="current-user-profile" style="position: relative;">
                    <div id="user-profile-trigger" style="display: flex; align-items: center; gap: 1rem; cursor: pointer; padding: 0.5rem; border-radius: 12px; transition: background 0.2s;" onmouseenter="this.style.background='var(--surface-hover)'" onmouseleave="this.style.background='transparent'">
                        <div style="text-align: right;">
                            <div style="font-weight: 600;" id="user-display-name">Guest User</div>
                            <div style="font-size: 0.75rem; color: var(--text-muted);" id="user-role">Resident</div>
                        </div>
                        <div id="user-avatar"
                            style="width: 40px; height: 40px; border-radius: 50%; background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 1.1rem;">
                            👤</div>
                    </div>

                    <!-- Dropdown Menu -->
                    <div id="user-dropdown" style="display: none; position: absolute; top: calc(100% + 0.5rem); right: 0; background: var(--surface-color); border: 1px solid var(--border-color); border-radius: 16px; padding: 0.75rem; min-width: 180px; box-shadow: var(--shadow-strong); z-index: 100;">
                        <a href="{{ route('settings') }}" style="display: block; padding: 0.6rem 0.75rem; color: var(--text-main); text-decoration: none; font-size: 0.875rem; border-radius: 8px;" onmouseenter="this.style.background='var(--surface-hover)'" onmouseleave="this.style.background='transparent'">⚙️ 設定</a>
                        <button onclick="openAppContactModal()" style="width: 100%; text-align: left; padding: 0.6rem 0.75rem; background: transparent; border: none; color: var(--text-main); cursor: pointer; font-size: 0.875rem; border-radius: 8px; font-family: inherit;" onmouseenter="this.style.background='var(--surface-hover)'" onmouseleave="this.style.background='transparent'">💬 お問い合わせ</button>
                        <hr style="border: 0; height: 1px; background: var(--border-color); margin: 0.5rem 0;">
                        <button onclick="logout()" style="width: 100%; text-align: left; padding: 0.6rem 0.75rem; background: transparent; border: none; color: var(--accent-color); cursor: pointer; font-size: 0.875rem; border-radius: 8px; font-family: inherit;">🚪 ログアウト</button>
                    </div>
                </div>
            </header>

            @yield('content')
        </main>
    </div>

    <!-- App Contact Modal -->
    <div id="app-contact-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); backdrop-filter: blur(4px); z-index: 2000; align-items: center; justify-content: center;">
        <div style="background: var(--surface-color); padding: 2rem; border-radius: 16px; width: 90%; max-width: 500px; border: 1px solid var(--border-color); box-shadow: 0 10px 25px rgba(0,0,0,0.5);">
            <h3 style="margin-top: 0; margin-bottom: 1.5rem; color: var(--primary-color);">お問い合わせ</h3>
            <form id="app-contact-form">
                <div style="margin-bottom: 1rem;">
                    <label style="display: block; font-size: 0.875rem; color: var(--text-muted); margin-bottom: 0.5rem;">登録名</label>
                    <input type="text" id="app-contact-name" readonly style="width: 100%; padding: 0.75rem; border-radius: 8px; border: 1px solid rgba(255,255,255,0.05); background: rgba(0,0,0,0.2); color: var(--text-muted); cursor: not-allowed; box-sizing: border-box;">
                </div>
                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; font-size: 0.875rem; color: var(--text-muted); margin-bottom: 0.5rem;">お問い合わせ内容</label>
                    <textarea id="app-contact-message" required rows="5" style="width: 100%; padding: 0.75rem; border-radius: 8px; border: 1px solid var(--border-color); background: var(--bg-color); color: var(--text-main); font-family: inherit; resize: vertical; box-sizing: border-box;"></textarea>
                </div>
                <div id="app-contact-result" style="display: none; margin-bottom: 1rem; font-size: 0.85rem; font-weight: 700; text-align: center; padding: 0.5rem; border-radius: 8px;"></div>
                <div style="display: flex; gap: 1rem;">
                    <button type="submit" id="app-contact-submit" style="flex: 1; padding:0.75rem; border-radius:8px; background:var(--primary-gradient); color:white; border:none; cursor:pointer; font-weight:700;">送信する</button>
                    <button type="button" style="flex: 1; padding:0.75rem; border-radius:8px; background:transparent; color:var(--text-muted); border:1px solid var(--border-color); cursor:pointer;" onclick="closeAppContactModal()">キャンセル</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Modal logic
        const openAppContactModal = async () => {
            const userName = localStorage.getItem('kkb_userName');
            document.getElementById('app-contact-name').value = userName || 'Member';
            document.getElementById('app-contact-result').style.display = 'none';
            document.getElementById('app-contact-modal').style.display = 'flex';
        };

        const closeAppContactModal = () => {
            document.getElementById('app-contact-modal').style.display = 'none';
            document.getElementById('app-contact-form').reset();
        };

        document.getElementById('app-contact-form').addEventListener('submit', async (e) => {
            e.preventDefault();
            const btn = document.getElementById('app-contact-submit');
            const resultMsg = document.getElementById('app-contact-result');
            
            btn.disabled = true;
            btn.textContent = '送信中...';
            resultMsg.style.display = 'none';

            // User info injected by backend, just pass empty email or let backend figure it out from X-User-Id
            const message = document.getElementById('app-contact-message').value;
            const name = document.getElementById('app-contact-name').value;

            try {
                const res = await fetch('/api/contacts', {
                    method: 'POST',
                    headers: { 
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-User-Id': localStorage.getItem('kkb_userId') || ''
                    },
                    // Send dummy email just to pass frontend validation. Backend ignores it if X-User-Id is valid
                    body: JSON.stringify({ name: name, email: 'user@example.com', message })
                });

                if (res.ok) {
                    resultMsg.textContent = '送信しました。';
                    resultMsg.style.color = '#10b981';
                    resultMsg.style.background = 'rgba(16, 185, 129, 0.1)';
                    setTimeout(() => {
                        closeAppContactModal();
                    }, 1500);
                } else {
                    const data = await res.json();
                    resultMsg.textContent = data.message || 'エラーが発生しました。';
                    resultMsg.style.color = '#ef4444';
                    resultMsg.style.background = 'rgba(239, 68, 68, 0.1)';
                }
            } catch (err) {
                resultMsg.textContent = '通信エラーが発生しました。';
                resultMsg.style.color = '#ef4444';
                resultMsg.style.background = 'rgba(239, 68, 68, 0.1)';
            }
            
            resultMsg.style.display = 'block';
            btn.disabled = false;
            btn.textContent = '送信する';
        });
    </script>

    @stack('scripts')
</body>

</html>
