<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }}</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>
    <nav class="main-nav">
        <div class="nav-container">
            <div class="nav-logo">
                <span>🌤️ {{ config('app.name') }}</span>
            </div>
            <ul class="nav-menu">
                <li><a href="{{ route('weather.index') }}" class="nav-link active">Главная</a></li>
                <li><a href="https://laravel.com/docs" class="nav-link" target="_blank" rel="noreferrer">Документация</a></li>
            </ul>
        </div>
    </nav>

    <div class="container">
        <div class="header">
            <h1>🌤️ Мониторинг погоды</h1>
            <p>Laravel + Postgres · учебный проект</p>
        </div>

        @if (session('status'))
            <div class="weather-card" style="border-left: 5px solid #00b894;">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="weather-card" style="border-left: 5px solid #d63031;">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if ($currentWeather)
            <div class="weather-card current-weather">
                <h2>Текущие показания</h2>
                <div class="weather-icon">{{ $currentWeather->icon }}</div>
                <div class="temperature">{{ number_format($currentWeather->temperature, 1) }}°C</div>
                <div class="weather-description">{{ $currentWeather->description }}</div>
                <div class="weather-details">
                    <div class="detail-item">
                        <div class="detail-label">Влажность</div>
                        <div class="detail-value">{{ $currentWeather->humidity }}%</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Давление</div>
                        <div class="detail-value">{{ $currentWeather->pressure }} hPa</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Ветер</div>
                        <div class="detail-value">{{ number_format($currentWeather->windSpeed, 1) }} м/с</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Измерено</div>
                        <div class="detail-value">
                            {{ optional($currentWeather->observedAt)?->format('d.m.Y H:i') ?? 'n/a' }}
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="weather-card">
                <h2>Нет показаний</h2>
                <p>Добавьте новую запись через форму ниже.</p>
            </div>
        @endif

        <div class="weather-card">
            <h2>Последние измерения</h2>
            <div class="weather-stats">
                @forelse ($history as $reading)
                    <div class="stat-card">
                        <div class="stat-value">{{ number_format($reading->temperature, 1) }}°C</div>
                        <div class="stat-label">{{ $reading->description }}</div>
                        <div class="stat-label">
                            {{ optional($reading->observedAt)?->format('d.m.Y H:i') ?? 'n/a' }}
                        </div>
                    </div>
                @empty
                    <p>История пуста.</p>
                @endforelse
            </div>
        </div>

        <div class="admin-panel">
            <h2>Добавить запись</h2>
            <form method="POST" action="{{ route('weather.store') }}">
                @csrf
                <div class="form-group">
                    <label for="temperature">Температура (°C)</label>
                    <input type="number" step="0.1" id="temperature" name="temperature" value="{{ old('temperature') }}" required>
                </div>
                <div class="form-group">
                    <label for="humidity">Влажность (%)</label>
                    <input type="number" min="0" max="100" id="humidity" name="humidity" value="{{ old('humidity') }}" required>
                </div>
                <div class="form-group">
                    <label for="pressure">Давление (hPa)</label>
                    <input type="number" id="pressure" name="pressure" value="{{ old('pressure') }}" required>
                </div>
                <div class="form-group">
                    <label for="wind_speed">Скорость ветра (м/с)</label>
                    <input type="number" step="0.1" id="wind_speed" name="wind_speed" value="{{ old('wind_speed') }}" required>
                </div>
                <div class="form-group">
                    <label for="description">Описание</label>
                    <select id="description" name="description" required>
                        @foreach (['Солнечно', 'Облачно', 'Пасмурно', 'Дождь', 'Снег'] as $option)
                            <option value="{{ $option }}" @selected(old('description') === $option)>{{ $option }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="icon">Иконка</label>
                    <select id="icon" name="icon" required>
                        @foreach (['☀️', '⛅', '☁️', '🌧️', '⛈️', '❄️'] as $icon)
                            <option value="{{ $icon }}" @selected(old('icon') === $icon)>{{ $icon }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="observed_at">Дата измерения</label>
                    <input type="datetime-local" id="observed_at" name="observed_at" value="{{ old('observed_at') }}">
                </div>
                <button type="submit" class="btn">Сохранить</button>
            </form>
        </div>
    </div>
</body>
</html>
