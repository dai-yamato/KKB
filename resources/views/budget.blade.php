@extends('layouts.app')

@section('title', 'KKB Premium | 予算管理')
@section('header_title', '予算管理 💰')
@section('header_subtitle', '今月の目標を設定して支出をコントロールしましょう')

@push('styles')
<style>
    .budget-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }

    .budget-item {
        display: flex;
        flex-direction: column;
        gap: 1rem;
        padding: 1.5rem;
        background: var(--surface-color);
        border-radius: 16px;
        border: 1px solid var(--border-color);
        transition: transform 0.2s ease, border-color 0.2s ease;
    }

    .budget-item:hover {
        border-color: var(--primary-color);
    }

    .budget-info {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .budget-name {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        font-weight: 600;
    }

    .budget-name span {
        font-size: 1.5rem;
    }

    .budget-amounts {
        text-align: right;
    }

    .budget-current {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--text-main);
    }

    .budget-total {
        font-size: 0.875rem;
        color: var(--text-muted);
    }

    .progress-container {
        width: 100%;
        height: 8px;
        background: var(--surface-hover);
        border-radius: 4px;
        overflow: hidden;
        position: relative;
    }

    .progress-bar {
        height: 100%;
        background: var(--primary-color);
        border-radius: 4px;
        transition: width 0.5s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .progress-status {
        display: flex;
        justify-content: space-between;
        font-size: 0.75rem;
        font-weight: 500;
    }

    .status-over {
        color: var(--accent-color);
    }

    .status-ok {
        color: var(--primary-color);
    }

    .budget-input-group {
        display: flex;
        gap: 0.5rem;
        margin-top: 0.5rem;
    }

    .budget-input-group input {
        flex: 1;
        padding: 0.5rem;
        border-radius: 8px;
        border: 1px solid var(--border-color);
        background: var(--surface-hover);
        color: var(--text-main);
        font-family: inherit;
    }

    .btn-save-budget {
        background: var(--primary-color);
        color: white;
        border: none;
        padding: 0.5rem 1rem;
        border-radius: 8px;
        cursor: pointer;
        font-size: 0.875rem;
        font-weight: 600;
    }

    @media (min-width: 768px) {
        .budget-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }
</style>
@endpush

@section('content')
<section class="summary-grid" style="margin-bottom: 2rem;">
    <div class="card" style="--card-accent: var(--primary-color);">
        <div class="card-label">今月の総予算</div>
        <div class="card-value" id="total-budget-view">¥0</div>
        <div class="card-trend" id="total-budget-remaining">残り ¥0</div>
    </div>
    <div class="card" style="--card-accent: var(--secondary-color);">
        <div class="card-label">予算消化率</div>
        <div class="card-value" id="budget-usage-percent">0%</div>
        <div class="progress-container" style="margin-top: 1rem; height: 12px;">
            <div id="total-budget-progress" class="progress-bar" style="width: 0%;"></div>
        </div>
    </div>
</section>

<div class="budget-grid" id="category-budgets">
    <!-- JS will inject budget items per category here -->
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/pages/budget.js') }}"></script>
@endpush
