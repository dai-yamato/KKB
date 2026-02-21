@extends('layouts.app')

@section('title', 'KKB Premium | 前月比較')
@section('header_title', '前月比較 ⚖️')
@section('header_subtitle', '月ごとの支出を比較して傾向を把握しましょう')

@push('styles')
<style>
    .comparison-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }

    .comparison-card {
        background: var(--surface-color);
        border-radius: 16px;
        border: 1px solid var(--border-color);
        padding: 1.5rem;
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .comp-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .comp-title {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        font-weight: 600;
    }

    .comp-title span {
        font-size: 1.5rem;
    }

    .comp-values {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
        margin-top: 0.5rem;
    }

    .val-box {
        padding: 1rem;
        border-radius: 12px;
        background: var(--surface-hover);
    }

    .val-label {
        font-size: 0.75rem;
        color: var(--text-muted);
        margin-bottom: 0.25rem;
    }

    .val-amount {
        font-size: 1.1rem;
        font-weight: 700;
    }

    .diff-indicator {
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-weight: 700;
        font-size: 0.875rem;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
    }

    .diff-up {
        background: rgba(244, 63, 94, 0.1);
        color: var(--accent-color);
    }

    .diff-down {
        background: rgba(16, 185, 129, 0.1);
        color: var(--primary-color);
    }

    .diff-neutral {
        background: var(--surface-hover);
        color: var(--text-muted);
    }

    @media (min-width: 768px) {
        .comparison-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }
</style>
@endpush

@section('content')
<div class="month-selector-group"
    style="display: flex; gap: 1rem; align-items: center; margin-bottom: 2rem;">
    <div class="form-group" style="margin-bottom: 0;">
        <label style="font-size: 0.75rem;">比較対象月</label>
        <input type="month" id="target-month-select" class="nav-item"
            style="background: var(--surface-color); border: 1px solid var(--border-color); padding: 0.5rem;">
    </div>
    <div style="font-size: 1.25rem; padding-top: 1rem;">vs</div>
    <div class="form-group" style="margin-bottom: 0;">
        <label style="font-size: 0.75rem;">基準月</label>
        <input type="month" id="base-month-select" class="nav-item"
            style="background: var(--surface-color); border: 1px solid var(--border-color); padding: 0.5rem;">
    </div>
</div>

<section class="summary-grid" style="margin-bottom: 2rem;">
    <div class="card" id="total-comp-card">
        <div class="card-label">総支出の比較</div>
        <div style="display: flex; align-items: baseline; gap: 0.5rem;">
            <span class="card-value" id="total-diff-percent">0%</span>
            <span id="total-diff-label" style="font-weight: 600;">増量</span>
        </div>
        <div class="card-trend" id="total-diff-amount">先月より +¥0</div>
    </div>
</section>

<div class="comparison-grid" id="category-comparison">
    <!-- JS will inject comparison items here -->
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/pages/comparison.js') }}"></script>
@endpush
