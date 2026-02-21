@extends('layouts.app')

@section('title', 'KKB Premium | 履歴一覧')
@section('header_title', '履歴一覧 📝')
@section('header_subtitle', '過去の入出金記録をすべて確認できます')

@section('content')
<div class="list-section" style="max-width: 800px; margin: 0 auto;">
    
    <!-- Filter Controls -->
    <div class="card" style="margin-bottom: 2rem;">
        <div style="display: flex; gap: 1rem; align-items: center; flex-wrap: wrap;">
            <div style="flex: 1; min-width: 150px;">
                <label style="display: block; font-size: 0.75rem; color: var(--text-muted); margin-bottom: 0.25rem;">期間（月）</label>
                <input type="month" id="month-filter" style="width: 100%; padding: 0.75rem; border-radius: 8px; border: 1px solid var(--border-color); background: var(--surface-color); color: var(--text-main);">
            </div>
            
            <div style="flex: 1; min-width: 150px;">
                <label style="display: block; font-size: 0.75rem; color: var(--text-muted); margin-bottom: 0.25rem;">種類</label>
                <select id="type-filter" style="width: 100%; padding: 0.75rem; border-radius: 8px; border: 1px solid var(--border-color); background: var(--surface-color); color: var(--text-main);">
                    <option value="all">すべて</option>
                    <option value="expense">支出 💸</option>
                    <option value="income">収入 💰</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Summary -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; padding: 0 0.5rem;">
        <h2 style="font-size: 1.1rem; font-weight: 700;">記録一覧</h2>
        <div style="font-size: 0.875rem; color: var(--text-muted);">
            合計: <span id="list-total" style="font-weight: 700; color: var(--text-main);">¥0</span>
        </div>
    </div>

    <!-- Transaction List -->
    <div class="history-card" style="padding: 0;">
        <div class="transaction-list" id="full-transaction-list">
            <p style="text-align: center; color: var(--text-muted); padding: 2rem;">データを読み込んでいます...</p>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/pages/list.js') }}?v={{ filemtime(public_path('js/pages/list.js')) }}"></script>
@endpush
