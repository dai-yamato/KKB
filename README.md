# 💎 KKB Premium (Kakeibo Premium)

KKB Premium は、美しさと機能性を兼ね備えた、マルチテナント（複数家計対応）型の次世代家計簿アプリケーションです。家族間でのデータ共有、詳細な統計分析、そして直感的な操作感を提供します。

## ✨ 特徴

- **マルチテナント構造**: 複数の「家庭」をサポート。各家庭は独立したデータ領域を持ち、セキュリティが保たれています。
- **モダンなUI・UX**: Glassmorphism を採用した美しいデザイン。ダークモード・ライトモードの切り替えに対応。
- **直感的なダッシュボード**: 今月の収支、予算進捗、最新の履歴を一目で把握可能。設定した「月の開始日」に合わせた期間計算を自動で行います。
- **サクサク連続入力モード**:
  - スマートフォンやPCから素早く記録できるテンキーUI。
  - 支出/収入の切り替えと、利用頻度が高い順に並ぶ美しい絵文字カテゴリチップ。
- **高度な分析機能**: 
  - **統計画面**: カテゴリ別、ユーザー別の支出割合をグラフで可視化(Chart.js)。
  - **前月比較**: 項目ごとの増減を％でレポートし、ムダ遣いを特定。
  - **カレンダー**: 日ごとの支出を視覚的に管理。
- **柔軟なカテゴリ管理**:
  - 「支出」と「収入」を明確に分離。
  - 豊富な絵文字ピッカーから1クリックでアイコンを選択可能。
- **予算管理**: カテゴリごとに月間予算を設定し、残額をリアルタイムにトラッキング。
- **チーム（家族）招待と権限管理**:
  - 設定画面から「招待リンク」を発行し、家族を簡単に家計に招待。
  - **👑 管理者 (Admin)** と **✍️ 入力者 (Editor)** の権限ロールにより、安全にメンバーを管理。
  - カスタムカテゴリの作成・削除処理は**管理者のみ**に制限。
  - **誤削除アンチパターン**: 入力者は「登録から24時間以上経過したデータ」の削除がロック（🔒）され、過去の改ざんを防止。

## 🛠 技術スタック

- **Backend**: Laravel 12 (PHP 8.3)
- **Database**: PostgreSQL 16
- **Frontend**: Vanilla JavaScript (ES6+), Vanilla CSS (Custom Design System, CSS Variables)
- **Visualization**: Chart.js
- **Environment**: Docker (PHP-FPM, Nginx, PostgreSQL)

## 🚀 セットアップ

### 前提条件
- Docker / Docker Compose

### 手順
1. **リポジトリの準備**
   ```bash
   git clone <repository_url>
   cd kkb/LaravelBase/laravel
   ```

2. **環境変数の編集**
   `laravel/.env.example` を `laravel/.env` にコピーし、必要に応じて DB 設定を変更します。

3. **Docker コンテナの起動**
   ```bash
   cd ..
   docker compose up -d --build
   ```

4. **初期設定**
   ```bash
   docker compose exec app composer install
   docker compose exec app php artisan key:generate
   docker compose exec app php artisan migrate:fresh --seed
   
   # サンプルとして前月データを作りたい場合
   docker compose exec app php artisan db:seed --class=PreviousMonthDataSeeder
   ```

### アクセス
- Web: `http://localhost` (Nginx経由)

### テストユーザー (シーダーで生成)
1. **👑 管理者**
   - Email: `yamada.taro@example.com`
   - Pass: `password`
2. **✍️ 入力者**
   - Email: `yamada.hanako@example.com`
   - Pass: `password`

## 📁 主要構成と設計思想

- `app/Http/Controllers`: RESTFulなAPIと画面連携。特に `CategoryController` は頻度順ソートを、`SettingController` は権限管理を担う。
- `app/Models`: Database連携（Household, User, Transaction, Category, Budget, HouseholdInvitation）
- `database/migrations`: 外部キー制約とカスケード削除の徹底。
- `resources/views`: UIコンポーネントごとのBlade。`kkb-settings.js`によるテーマの即時反映。
- `public/js`: グローバル設定 (`kkb-settings.js`) とページごとのロジック分離。
- `public/css`: CSS変数（Custom Properties）を駆使したテーマ切り替えとGlassmorphismUI。

---
Crafted with high aesthetics for better financial life.
