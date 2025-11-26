@php $title = 'О проекте'; @endphp
@extends('layouts.app')

@section('content')
<div class="header">
    <h1>📊 {{ $strings['nav_about'] ?? 'О проекте' }}</h1>
    <p>Краткое описание технологического стека и возможностей сервиса</p>
    <div class="muted">{{ $preferences['login'] ?? '' }}</div>
</div>

<div class="weather-card">
    <h2>О системе</h2>
    <p>Проект разворачивается в Docker Compose и работает как единое Laravel-приложение.</p>
    <div class="weather-details">
        <div class="detail-item">
            <div class="detail-label">Веб</div>
            <div class="detail-value">Nginx → PHP-FPM (Laravel)</div>
        </div>
        <div class="detail-item">
            <div class="detail-label">База</div>
            <div class="detail-value">PostgreSQL 13</div>
        </div>
        <div class="detail-item">
            <div class="detail-label">Кеш/сессии</div>
            <div class="detail-value">Redis</div>
        </div>
        <div class="detail-item">
            <div class="detail-label">Графики</div>
            <div class="detail-value">JpGraph + GD</div>
        </div>
    </div>
</div>

<div class="weather-card">
    <h2>Особенности</h2>
    <ul style="list-style:none; line-height:1.8; padding-left:0;">
        <li>✅ REST API для погоды и пользователей</li>
        <li>✅ Генерация графиков и демо-фикстур</li>
        <li>✅ Загрузка/хранение PDF в storage/public</li>
        <li>✅ Персональные настройки (тема/язык) через сессию и cookies</li>
        <li>✅ Адаптивный интерфейс без внешних UI-фреймворков</li>
    </ul>
</div>
@endsection
