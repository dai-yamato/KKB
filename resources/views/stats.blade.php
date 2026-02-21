@extends('layouts.app')

@section('title', 'KKB Premium | 統計・レポート')
@section('header_title', '統計・レポート 📈')
@section('header_subtitle', '収支の傾向を分析しましょう')

@push('styles')
<style>
    .stats-container {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
        gap: 1.5rem;
    }

    .chart-card {
        padding: 2rem;
    }

    .chart-wrapper {
        position: relative;
        height: 300px;
        width: 100%;
    }
</style>
@endpush

@section('content')
<div class="stats-container">
    <!-- Main Spending Chart -->
    <div class="card chart-card">
        <h2 style="margin-bottom: 1.5rem; font-size: 1.25rem;">支出の推移</h2>
        <div class="chart-wrapper">
            <canvas id="balance-chart"></canvas>
        </div>
    </div>

    <!-- Category Breakdown -->
    <div class="card chart-card">
        <h2 style="margin-bottom: 1.5rem; font-size: 1.25rem;">カテゴリー別割合</h2>
        <div class="chart-wrapper">
            <canvas id="category-chart"></canvas>
        </div>
    </div>

    <!-- 6 Month Trend -->
    <div class="card chart-card" style="grid-column: 1 / -1;">
        <h2 style="margin-bottom: 1.5rem; font-size: 1.25rem;">過去6ヶ月の支出推移</h2>
        <div class="chart-wrapper" style="height: 300px;">
            <canvas id="trend-6month-chart"></canvas>
        </div>
    </div>
</div>

<!-- Detailed Analysis Table -->
<div class="card" style="margin-top: 2rem;">
    <h2 style="margin-bottom: 1.5rem; font-size: 1.25rem;">カテゴリー別詳細</h2>
    <div id="category-details-list" style="display: flex; flex-direction: column; gap: 1rem;">
        <!-- JS will inject breakdown here -->
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="{{ asset('js/pages/stats.js') }}"></script>
@endpush
