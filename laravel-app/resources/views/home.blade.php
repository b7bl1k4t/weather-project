@php
    $title = $strings['page_title'] ?? 'Weather';
    $windUnit = ($preferences['language'] ?? 'ru') === 'ru' ? 'м/с' : 'm/s';
@endphp
@extends('layouts.app')

@section('content')
    <div class="header">
        <h1>{{ $strings['hero_title'] ?? '' }}</h1>
        <p>{{ $strings['hero_subtitle'] ?? '' }}</p>
    </div>

    <div class="weather-grid">
        <div class="weather-card current-weather">
            <h2>{{ $strings['current_weather_title'] ?? '' }}</h2>
            @if($latest)
                <div class="weather-icon">{{ $latest->icon }}</div>
                <div class="temperature">{{ $latest->temperature }}°C</div>
                <div class="weather-description">{{ $latest->description }}</div>
                <div class="weather-details">
                    <div class="detail-item">
                        <div class="detail-label">{{ $strings['detail_humidity'] ?? '' }}</div>
                        <div class="detail-value">{{ $latest->humidity }}%</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">{{ $strings['detail_pressure'] ?? '' }}</div>
                        <div class="detail-value">{{ $latest->pressure }} hPa</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">{{ $strings['detail_wind'] ?? '' }}</div>
                        <div class="detail-value">{{ $latest->wind_speed }} {{ $windUnit }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">{{ $strings['detail_updated'] ?? '' }}</div>
                        <div class="detail-value">{{ $latest->created_at }}</div>
                    </div>
                </div>
            @else
                <p>{{ $strings['no_weather'] ?? '' }}</p>
                <p class="muted">{{ $strings['no_weather_hint'] ?? '' }}</p>
            @endif
        </div>

        <div class="weather-card">
            <h2>{{ $strings['preferences_title'] ?? '' }}</h2>
            <p class="muted">{{ $strings['preferences_subtitle'] ?? '' }}</p>
            <div class="weather-details" style="grid-template-columns: 1fr;">
                <div class="detail-item" style="text-align:left;">
                    <div><strong>{{ $strings['greeting'] ?? '' }}, {{ $preferences['login'] }}</strong></div>
                    <div>{{ $strings['theme_current'] ?? '' }}: {{ $themeNames[$preferences['theme']][$preferences['language']] ?? $preferences['theme'] }}</div>
                    <div>{{ $strings['language_current'] ?? '' }}: {{ $languageOptions[$preferences['language']] ?? $preferences['language'] }}</div>
                </div>
            </div>
            <form action="{{ route('preferences.store') }}" method="post" class="form">
                @csrf
                <div class="form-group">
                    <label for="login">{{ $strings['login_label'] ?? '' }}</label>
                    <input type="text" id="login" name="login" value="{{ $preferences['login'] }}" maxlength="40" required>
                </div>
                <div class="form-group">
                    <label for="theme">{{ $strings['theme_label'] ?? '' }}</label>
                    <select id="theme" name="theme">
                        @foreach($themeNames as $value => $names)
                            <option value="{{ $value }}" {{ $preferences['theme'] === $value ? 'selected' : '' }}>
                                {{ $names[$preferences['language']] ?? $value }}
                            </option>
                        @endforeach
                    </select>
                </div>
                    <div class="form-group">
                        <label for="language">{{ $strings['language_label'] ?? '' }}</label>
                        <select id="language" name="language">
                            @foreach($languageOptions as $value => $label)
                                <option value="{{ $value }}" {{ $preferences['language'] === $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                <button type="submit" class="btn">{{ $strings['preferences_submit'] ?? '' }}</button>
            </form>
        </div>
    </div>

    @if($history->count())
    <div class="weather-card">
        <h2>{{ $strings['history_title'] ?? '' }}</h2>
        <div class="weather-stats">
            @foreach($history as $record)
                <div class="stat-card" style="background: linear-gradient(135deg, #74b9ff, #0984e3);">
                    <div class="stat-value">{{ $record->temperature }}°C</div>
                    <div class="stat-label">{{ $record->description }}</div>
                    <div class="stat-label">{{ $record->created_at }}</div>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    <div class="weather-card">
        <h2>{{ $strings['form_title'] ?? '' }}</h2>
        <form action="{{ route('weather.store') }}" method="post">
            @csrf
            <div class="form-group">
                <label for="temperature">{{ $strings['form_temperature'] ?? '' }}</label>
                <input type="number" id="temperature" name="temperature" step="0.1" required>
            </div>
            <div class="form-group">
                <label for="humidity">{{ $strings['form_humidity'] ?? '' }}</label>
                <input type="number" id="humidity" name="humidity" min="0" max="100" required>
            </div>
            <div class="form-group">
                <label for="pressure">{{ $strings['form_pressure'] ?? '' }}</label>
                <input type="number" id="pressure" name="pressure" required>
            </div>
            <div class="form-group">
                <label for="wind_speed">{{ $strings['form_wind'] ?? '' }}</label>
                <input type="number" id="wind_speed" name="wind_speed" step="0.1" required>
            </div>
            <div class="form-group">
                <label for="description">{{ $strings['form_description'] ?? '' }}</label>
                <input type="text" id="description" name="description" required>
            </div>
            <div class="form-group">
                <label for="icon">{{ $strings['form_icon'] ?? '' }}</label>
                <input type="text" id="icon" name="icon" maxlength="10" placeholder="☀️" required>
            </div>
            <button type="submit" class="btn">{{ $strings['form_submit'] ?? '' }}</button>
        </form>
    </div>

    <div class="weather-card">
        <h2>{{ $strings['files_title'] ?? '' }}</h2>
        <p class="muted">{{ $strings['files_subtitle'] ?? '' }}</p>
        <form action="{{ route('uploads.store') }}" method="post" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label for="file">{{ $strings['file_input'] ?? '' }}</label>
                <input type="file" id="file" name="file" accept="application/pdf" required>
            </div>
            <button type="submit" class="btn">{{ $strings['file_submit'] ?? '' }}</button>
        </form>

        <h3 style="margin-top:16px;">{{ $strings['file_list_title'] ?? '' }}</h3>
        @if($uploads->isEmpty())
            <p class="muted">{{ $strings['file_empty'] ?? '' }}</p>
        @else
            <div class="weather-details" style="grid-template-columns: 1fr;">
                @foreach($uploads as $file)
                    <div class="detail-item" style="text-align:left;">
                        <div><strong>{{ $file->original_name }}</strong></div>
                        <div class="muted">
                            {{ $strings['uploaded_by'] ?? '' }}: {{ $file->uploaded_by ?? '—' }},
                            {{ $strings['uploaded_at'] ?? '' }} {{ $file->created_at }}
                        </div>
                        <div class="flex" style="margin-top:8px;">
                            <a class="btn" style="background:#e2e8f0; color:#0f172a; text-decoration:none;" href="{{ route('uploads.download', $file->id) }}">{{ $strings['download'] ?? '' }}</a>
                            <form action="{{ route('uploads.destroy', $file->id) }}" method="post" onsubmit="return confirm('{{ $strings['delete_confirm'] ?? '' }}')">
                                @csrf
                                @method('delete')
                                <button type="submit" class="btn" style="background:#ef4444;">{{ $strings['delete'] ?? '' }}</button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endsection
