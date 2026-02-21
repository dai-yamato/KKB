@extends('layouts.app')

@section('content')
<!-- Summary Statistic Cards -->
<section class="summary-grid">
    <div class="card" style="--card-accent: var(--primary-color);">
        <div class="card-label">今月の総収入</div>
        <div class="card-value" id="total-income">¥0</div>
        <div class="card-trend trend-up">↑ 12% 先月比</div>
    </div>
    <div class="card" style="--card-accent: var(--accent-color);">
        <div class="card-label">今月の総支出</div>
        <div class="card-value" id="total-expense">¥0</div>
        <div class="card-trend trend-down">↓ 5% 先月比</div>
    </div>
    <div class="card" style="--card-accent: var(--secondary-color);">
        <div class="card-label">現在の残高</div>
        <div class="card-value" id="total-balance">¥0</div>
    </div>
    <div class="card" style="--card-accent: #f59e0b;">
        <div class="card-label">予算消化状況</div>
        <div class="card-value" id="budget-summary-percent">0%</div>
        <div class="progress-container"
            style="margin-top: 0.5rem; background: var(--surface-hover); height: 6px; border-radius: 3px; overflow: hidden;">
            <div id="budget-summary-bar"
                style="height: 100%; background: #f59e0b; width: 0%; transition: width 0.5s ease;"></div>
        </div>
    </div>
</section>

<!-- Input & History Grid -->
<section class="input-section">
    <!-- Transaction Form -->
    <div class="form-card">
        <h2 style="margin-bottom: 1.5rem; font-size: 1.25rem;">新規入力</h2>
        <form id="transaction-form">
            <div class="form-group">
                <label for="date">日付</label>
                <input type="date" id="date" required>
            </div>
            <div class="form-group">
                <label for="type">区分</label>
                <select id="type">
                    <option value="expense">支出</option>
                    <option value="income">収入</option>
                </select>
            </div>
            <div class="form-group">
                <label for="category">カテゴリー</label>
                <select id="category">
                    <!-- JS will inject categories here -->
                </select>
            </div>
            <div class="form-group">
                <label for="amount">金額 (JPY)</label>
                <input type="number" id="amount" placeholder="5000" required>
            </div>
            <div class="form-group">
                <label for="note">メモ</label>
                <input type="text" id="note" placeholder="ランチ、買い物など">
            </div>
            <button type="submit" class="btn-primary">記録を追加する</button>
        </form>
    </div>

    <!-- Recent History -->
    <div class="form-card history-card">
        <h2 style="margin-bottom: 1.5rem; font-size: 1.25rem;">最近の履歴</h2>
        <div class="transaction-list" id="transaction-list">
            <!-- History items will be injected here -->
            <div style="text-align: center; color: var(--text-muted); padding-top: 3rem;">
                データがありません
            </div>
        </div>
    </div>
</section>
@endsection
