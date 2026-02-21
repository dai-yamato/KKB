@extends('layouts.app')

@section('title', 'KKB Premium | カレンダー')
@section('header_title', 'カレンダー 📅')
@section('header_subtitle', '日ごとの詳細を確認しましょう')

@push('styles')
<style>
    .calendar-card {
        background: var(--surface-color);
        border-radius: 24px;
        padding: 2rem;
        border: 1px solid var(--border-color);
    }

    .calendar-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
    }

    .calendar-grid {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 1px;
        background: var(--border-color);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        overflow: hidden;
    }

    .calendar-day-label {
        background: var(--surface-hover);
        padding: 1rem;
        text-align: center;
        font-weight: 700;
        font-size: 0.875rem;
        color: var(--text-muted);
    }

    .calendar-day {
        background: var(--surface-color);
        min-height: 120px;
        padding: 0.75rem;
        transition: background 0.2s ease;
    }

    .calendar-day:hover {
        background: var(--surface-hover);
    }

    .calendar-day.today {
        background: rgba(16, 185, 129, 0.05);
    }

    .calendar-day.today .day-number {
        background: var(--primary-color);
        color: white;
        width: 28px;
        height: 28px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
    }

    .day-number {
        font-weight: 600;
        margin-bottom: 0.5rem;
        display: block;
    }

    .day-events {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .event-tag {
        font-size: 0.7rem;
        padding: 2px 6px;
        border-radius: 4px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .event-income {
        background: rgba(16, 185, 129, 0.15);
        color: #10b981;
    }

    .event-expense {
        background: rgba(244, 63, 94, 0.15);
        color: #f43f5e;
    }

    .other-month {
        opacity: 0.3;
    }
</style>
@endpush

@section('content')
<div class="calendar-card">
    <div class="calendar-header">
        <h2 id="current-month">2026年 2月</h2>
        <div style="display: flex; gap: 0.5rem;">
            <button class="nav-item" id="prev-month"
                style="background: var(--surface-hover); border: none; padding: 0.5rem 1rem;">&lt;</button>
            <button class="nav-item" id="next-month"
                style="background: var(--surface-hover); border: none; padding: 0.5rem 1rem;">&gt;</button>
        </div>
    </div>

    <div class="calendar-grid" id="calendar-grid">
        <div class="calendar-day-label">日</div>
        <div class="calendar-day-label">月</div>
        <div class="calendar-day-label">火</div>
        <div class="calendar-day-label">水</div>
        <div class="calendar-day-label">木</div>
        <div class="calendar-day-label">金</div>
        <div class="calendar-day-label">土</div>
        <!-- Days will be injected by JS -->
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/pages/calendar.js') }}"></script>
@endpush
