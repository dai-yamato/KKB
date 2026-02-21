<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KKB Premium | 次世代の家計簿体験</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700;800&family=Noto+Sans+JP:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #10b981;
            --secondary: #3b82f6;
            --accent: #f43f5e;
            --bg: #0f172a;
            --surface: #1e293b;
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --glass: rgba(30, 41, 59, 0.7);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Outfit', 'Noto Sans JP', sans-serif;
            background-color: var(--bg);
            color: var(--text-main);
            overflow-x: hidden;
            line-height: 1.6;
        }

        /* Ambient Background */
        .ambient-bg {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            overflow: hidden;
        }

        .blob {
            position: absolute;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(16, 185, 129, 0.1) 0%, rgba(0,0,0,0) 70%);
            border-radius: 50%;
            filter: blur(80px);
            animation: move 20s infinite alternate;
        }

        .blob-2 {
            top: 50%;
            right: -100px;
            background: radial-gradient(circle, rgba(59, 130, 246, 0.1) 0%, rgba(0,0,0,0) 70%);
            animation-duration: 25s;
        }

        @keyframes move {
            from { transform: translate(0, 0); }
            to { transform: translate(100px, 100px); }
        }

        /* Navigation */
        nav {
            padding: 2rem 5%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: fixed;
            width: 100%;
            top: 0;
            background: var(--glass);
            backdrop-filter: blur(12px);
            z-index: 1000;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }

        .logo {
            font-size: 1.5rem;
            font-weight: 800;
            background: linear-gradient(to right, var(--primary), var(--secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .nav-links {
            display: flex;
            gap: 2rem;
            align-items: center;
        }

        .nav-links a {
            color: var(--text-main);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s;
        }

        .nav-links a:hover {
            color: var(--primary);
        }

        .btn-cta {
            background: var(--primary);
            color: white;
            padding: 0.8rem 1.5rem;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 700;
            transition: all 0.3s;
            box-shadow: 0 10px 20px rgba(16, 185, 129, 0.2);
        }

        .btn-cta:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 30px rgba(16, 185, 129, 0.4);
        }

        /* Hero */
        .hero {
            padding: 12rem 5% 6rem;
            text-align: center;
            max-width: 1000px;
            margin: 0 auto;
        }

        .badge {
            display: inline-block;
            padding: 0.5rem 1rem;
            background: rgba(16, 185, 129, 0.1);
            color: var(--primary);
            border-radius: 50px;
            font-size: 0.875rem;
            font-weight: 700;
            margin-bottom: 2rem;
            border: 1px solid rgba(16, 185, 129, 0.2);
        }

        h1 {
            font-size: 4rem;
            line-height: 1.1;
            margin-bottom: 1.5rem;
            letter-spacing: -2px;
        }

        .hero p {
            font-size: 1.25rem;
            color: var(--text-muted);
            margin-bottom: 3rem;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }

        /* Mockup */
        .mockup-container {
            position: relative;
            margin-top: 4rem;
            perspective: 1000px;
        }

        .mockup {
            width: 90%;
            max-width: 1100px;
            aspect-ratio: 16/9;
            background: var(--surface);
            margin: 0 auto;
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 50px 100px rgba(0, 0, 0, 0.5);
            overflow: hidden;
            transform: rotateX(10deg);
            transition: transform 0.5s ease;
        }

        .mockup:hover {
            transform: rotateX(0deg) translateY(-20px);
        }

        .mockup img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* Features */
        .features {
            padding: 10rem 5%;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            max-width: 1200px;
            margin: 0 auto;
        }

        .feature-card {
            background: var(--surface);
            padding: 3rem;
            border-radius: 32px;
            border: 1px solid rgba(255, 255, 255, 0.05);
            transition: all 0.3s;
        }

        .feature-card:hover {
            border-color: var(--primary);
            transform: translateY(-10px);
        }

        .feature-icon {
            font-size: 2.5rem;
            margin-bottom: 1.5rem;
            display: block;
        }

        .feature-card h3 {
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }

        .feature-card p {
            color: var(--text-muted);
        }

        /* Footer */
        footer {
            padding: 4rem 5%;
            text-align: center;
            border-top: 1px solid rgba(255, 255, 255, 0.05);
            color: var(--text-muted);
        }

        @media (max-width: 768px) {
            h1 { font-size: 2.2rem; letter-spacing: -1px; }
            .hero { padding: 8rem 5% 4rem; }
            .badge { margin-bottom: 1.5rem; }
            .nav-links { display: none; }
        }
    </style>
</head>
<body>
    <div class="ambient-bg">
        <div class="blob"></div>
        <div class="blob blob-2"></div>
    </div>

    <nav>
        <div class="logo"><span>💎</span> KKB Premium</div>
        <div class="nav-links">
            <a href="#features">機能</a>
            <a href="{{ route('register') }}">新規登録</a>
            <a href="/login" class="btn-cta">ログイン</a>
        </div>
    </nav>

    <header class="hero">
        <span class="badge">Beta版</span>
        <h1>家計管理を、<br><span style="color: var(--primary);">思考を加速させる</span>体験に。</h1>
        <p>直感的なUI、リアルタイム分析、そして家族との共有。KKB Premiumはあなたの資産形成を美しくサポートします。</p>
        <div class="mockup-container" style="margin-bottom: 2rem;">
            <div class="mockup">
                <img src="{{ asset('images/lp/dashboard.png') }}" alt="KKB Dashboard Mockup" id="mockup-img">
            </div>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; max-width: 1100px; margin: 0 auto; text-align: center; padding: 0 5%;">
            <div style="background: var(--surface); border-radius: 16px; padding: 1rem; border: 1px solid rgba(255,255,255,0.05); box-shadow: 0 10px 30px rgba(0,0,0,0.3); transition: transform 0.3s;" onmouseover="this.style.transform='translateY(-5px)'" onmouseout="this.style.transform='none'">
                <img src="{{ asset('images/lp/stats.png') }}" alt="Stats Analysis" style="width: 100%; border-radius: 8px; margin-bottom: 1rem;">
                <h4 style="font-size: 1rem; margin-bottom: 0.25rem;">高度な統計・分析</h4>
                <p style="font-size: 0.8rem; color: var(--text-muted); margin:0; line-height: 1.4;">カテゴリ別・ユーザー別の支出を美しいグラフで直感的に可視化します。</p>
            </div>
            <div style="background: var(--surface); border-radius: 16px; padding: 1rem; border: 1px solid rgba(255,255,255,0.05); box-shadow: 0 10px 30px rgba(0,0,0,0.3); transition: transform 0.3s;" onmouseover="this.style.transform='translateY(-5px)'" onmouseout="this.style.transform='none'">
                <img src="{{ asset('images/lp/list.png') }}" alt="Transaction List" style="width: 100%; border-radius: 8px; margin-bottom: 1rem;">
                <h4 style="font-size: 1rem; margin-bottom: 0.25rem;">詳細な履歴と権限管理</h4>
                <p style="font-size: 0.8rem; color: var(--text-muted); margin:0; line-height: 1.4;">日付ごとに自動整理。管理者は過去データの修正・削除も安全に行えます。</p>
            </div>
            <div style="background: var(--surface); border-radius: 16px; padding: 1rem; border: 1px solid rgba(255,255,255,0.05); box-shadow: 0 10px 30px rgba(0,0,0,0.3); transition: transform 0.3s;" onmouseover="this.style.transform='translateY(-5px)'" onmouseout="this.style.transform='none'">
                <div style="height: 140px; overflow: hidden; margin-bottom: 1rem; display: flex; justify-content: center;">
                    <img src="{{ asset('images/lp/input.png') }}" alt="Mobile Input" style="height: 100%; width: auto; object-fit: contain; border-radius: 8px;">
                </div>
                <h4 style="font-size: 1rem; margin-bottom: 0.25rem;">スマホ特化のサクサク入力</h4>
                <p style="font-size: 0.8rem; color: var(--text-muted); margin:0; line-height: 1.4;">スクロール不要のコンパクトUI。レシートが溜まっても素早く連続記録。</p>
            </div>
        </div>
    </header>

    <section class="features" id="features">
        <div class="feature-card">
            <span class="feature-icon">📊</span>
            <h3>リアルタイム統計・一覧</h3>
            <p>支出の傾向を可視化し、日付ごとの詳細な履歴一覧を用意。どこにお金を使ったか一目で把握できます。</p>
        </div>
        <div class="feature-card">
            <span class="feature-icon">📱</span>
            <h3>スマホ最適化の連続入力</h3>
            <p>スクロール不要のコンパクトなスマホ表示。テンキーUIでレシートが溜まってもサクサク記録完了。</p>
        </div>
        <div class="feature-card">
            <span class="feature-icon">👑</span>
            <h3>管理者と入力者の権限管理</h3>
            <p>家族を招待して共同管理。管理者のみカテゴリ変更が可能で、重要な設定を守ります。</p>
        </div>
        <div class="feature-card">
            <span class="feature-icon">🔒</span>
            <h3>過去データの安全保護</h3>
            <p>入力者は登録から24時間経過したデータを削除不可。誤操作や過去の改ざんを自動ブロックします。</p>
        </div>
        <div class="feature-card">
            <span class="feature-icon">⚖️</span>
            <h3>前月比較・予算アラート</h3>
            <p>カテゴリごとの予算と、先月との増減を％でレポート。ムダ遣いを事前に察知します。</p>
        </div>
        <div class="feature-card">
            <span class="feature-icon">🎨</span>
            <h3>直感的なカテゴリ管理</h3>
            <p>豊富な絵文字ピッカーから1クリックでアイコンを選択し、直感的な支出・収入の分類が可能です。</p>
        </div>
    </section>

    <footer>
        <p>&copy; 2026 KKB Premium. Crafted with high aesthetics.</p>
    </footer>

    <script>
        // Smooth reveal on scroll
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, { threshold: 0.1 });

        document.querySelectorAll('.feature-card').forEach(card => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(30px)';
            card.style.transition = 'all 0.6s ease-out';
            observer.observe(card);
        });
    </script>
</body>
</html>
