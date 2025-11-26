@php
    $title = $strings['page_title'] ?? 'Статистика';
    $cacheBuster = time();
@endphp
@extends('layouts.app')

@section('content')
    <div class="header">
        <h1>{{ $strings['header_title'] ?? 'Статистика и графики' }}</h1>
        <p class="muted" style="color:white; opacity:0.9;">{{ $strings['header_subtitle'] ?? '' }}</p>
    </div>

    <div class="weather-card">
        @if($dbError ?? false)
            <p class="muted">{{ $strings['db_error'] ?? 'Ошибка' }}: {{ $dbError }}</p>
        @else
            <div class="weather-stats">
                <div class="stat-card">
                    <div class="stat-value">{{ $count }}</div>
                    <div class="stat-label">{{ $strings['summary_total'] ?? '' }}</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">{{ $lastRecord ?? ($strings['no_records'] ?? '—') }}</div>
                    <div class="stat-label">{{ $strings['summary_latest'] ?? '' }}</div>
                </div>
                <div class="stat-card" style="background: linear-gradient(135deg,#22c55e,#16a34a);">
                    <div class="stat-value">{{ $strings['summary_hint'] ?? '' }}</div>
                </div>
            </div>
            @if(($autofilled ?? 0) > 0)
                <div class="muted" style="margin-top:12px;">
                    {{ sprintf($strings['autofill_note'] ?? '', $autofilled) }}
                </div>
            @endif
        @endif
    </div>

    <div class="weather-card">
        <h2>{{ $strings['charts_daily'] ?? 'Графики' }}</h2>
        <div class="chart-grid">
            <div class="chart-card">
                <h3>{{ $strings['charts_daily'] ?? 'Сутки' }}</h3>
                <img data-chart-thumb data-full="{{ url('/api/charts/daily') }}?v={{ $cacheBuster }}" class="chart" src="{{ url('/api/charts/daily') }}?v={{ $cacheBuster }}" alt="{{ $strings['charts_daily'] ?? '' }}">
                <p class="muted">{{ $strings['charts_hint_day'] ?? '' }}</p>
            </div>
            <div class="chart-card">
                <h3>{{ $strings['charts_weekly'] ?? 'Неделя' }}</h3>
                <img data-chart-thumb data-full="{{ url('/api/charts/weekly') }}?v={{ $cacheBuster }}" class="chart" src="{{ url('/api/charts/weekly') }}?v={{ $cacheBuster }}" alt="{{ $strings['charts_weekly'] ?? '' }}">
                <p class="muted">{{ $strings['charts_hint_week'] ?? '' }}</p>
            </div>
            <div class="chart-card">
                <h3>{{ $strings['charts_monthly'] ?? 'Месяц' }}</h3>
                <img data-chart-thumb data-full="{{ url('/api/charts/monthly') }}?v={{ $cacheBuster }}" class="chart" src="{{ url('/api/charts/monthly') }}?v={{ $cacheBuster }}" alt="{{ $strings['charts_monthly'] ?? '' }}">
                <p class="muted">{{ $strings['charts_hint_month'] ?? '' }}</p>
            </div>
        </div>
        <p class="muted" style="margin-top:8px;">Нажмите на график, чтобы открыть его в большом окне.</p>
    </div>

    <div class="weather-card">
        <h2>{{ $strings['fixtures_title'] ?? 'Фикстуры' }}</h2>
        <p class="muted">{{ $strings['fixtures_fill'] ?? '' }}</p>
        <form action="{{ route('fixtures.fill') }}" method="post" class="flex" style="margin-bottom:8px;">
            @csrf
            <input type="hidden" name="count" value="60">
            <input type="hidden" name="minimum" value="50">
            <button type="submit" class="btn">{{ $strings['cta_generate'] ?? 'Добавить' }}</button>
        </form>
        <p class="muted">{{ $strings['fixtures_reset'] ?? '' }}</p>
        <form action="{{ route('fixtures.reset') }}" method="post" class="flex">
            @csrf
            <input type="hidden" name="count" value="80">
            <button type="submit" class="btn" style="background:#ef4444;">{{ $strings['cta_reset'] ?? 'Пересоздать' }}</button>
        </form>
    </div>
@endsection
