@php
    $title = 'Админка';
@endphp
@extends('layouts.app')

@section('content')
    <div class="header">
        <h1>⚙️ {{ $strings['nav_admin'] ?? 'Админка' }}</h1>
        <p class="muted" style="color:white; opacity:0.9;">Панель администратора</p>
    </div>

    <div class="weather-card">
        <h2>Состояние данных</h2>
        <div class="weather-details">
            <div class="detail-item">
                <div class="detail-label">Записей погоды</div>
                <div class="detail-value">{{ $weatherCount }}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Последняя запись</div>
                <div class="detail-value">{{ $lastWeather ?: '—' }}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Пользователей</div>
                <div class="detail-value">{{ $usersCount }}</div>
            </div>
        </div>
        <div style="margin-top:16px;" class="flex">
            <a class="btn" href="{{ route('stats') }}">Статистика</a>
            <a class="btn" style="background:#e2e8f0; color:#0f172a; text-decoration:none;" href="{{ route('home') }}">На главную</a>
        </div>
    </div>

    <div class="weather-card">
        <h2>API ссылки</h2>
        <div class="weather-details" style="grid-template-columns:1fr;">
            <div class="detail-item" style="text-align:left;">
                <div><strong>Weather API</strong></div>
                <div class="muted">GET/POST/PUT/DELETE: /api/weather</div>
            </div>
            <div class="detail-item" style="text-align:left;">
                <div><strong>Users API</strong></div>
                <div class="muted">GET/POST/PUT/DELETE: /api/users</div>
            </div>
            <div class="detail-item" style="text-align:left;">
                <div><strong>Uploads</strong></div>
                <div class="muted">GET /api/uploads, POST /api/uploads, DELETE /api/uploads/{id}</div>
            </div>
        </div>
    </div>
@endsection
