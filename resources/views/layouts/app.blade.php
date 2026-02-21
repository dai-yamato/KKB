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
                        <hr style="border: 0; height: 1px; background: var(--border-color); margin: 0.5rem 0;">
                        <button onclick="logout()" style="width: 100%; text-align: left; padding: 0.6rem 0.75rem; background: transparent; border: none; color: var(--accent-color); cursor: pointer; font-size: 0.875rem; border-radius: 8px; font-family: inherit;">🚪 ログアウト</button>
                    </div>
                </div>
            </header>

            @yield('content')
        </main>
    </div>

    @stack('scripts')
</body>

</html>
