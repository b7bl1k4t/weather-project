@php
    $title = 'Погода — главная';
@endphp
@extends('layouts.app')

@section('content')
    <div class="card">
        <h1>Динамические данные</h1>
        <p class="muted">Последняя запись из базы и история.</p>
        @if($latest)
            <h2>Текущая погода</h2>
            <div class="flex">
                <div>
                    <div style="font-size: 2.5rem">{{ $latest->icon }}</div>
                    <div style="font-size: 2rem; font-weight: 700">{{ $latest->temperature }}°C</div>
                    <div>{{ $latest->description }}</div>
                </div>
                <div class="muted">
                    <div>Влажность: {{ $latest->humidity }}%</div>
                    <div>Давление: {{ $latest->pressure }} hPa</div>
                    <div>Ветер: {{ $latest->wind_speed }} м/с</div>
                    <div>Обновлено: {{ $latest->created_at }}</div>
                </div>
            </div>
        @else
            <p>Данных пока нет.</p>
        @endif

        @if($history->count())
            <h3>История</h3>
            <div class="list">
                @foreach($history as $record)
                    <div class="card" style="margin:0;padding:12px">
                        <div style="font-size: 1.2rem; font-weight:600">{{ $record->temperature }}°C — {{ $record->description }}</div>
                        <div class="muted">{{ $record->created_at }}</div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <div class="card">
        <h2>Добавить данные о погоде</h2>
        <form action="{{ route('weather.store') }}" method="post" class="flex" style="flex-direction: column">
            @csrf
            <div class="field">
                <label for="temperature">Температура (°C)</label>
                <input type="number" step="0.1" name="temperature" id="temperature" required>
            </div>
            <div class="field">
                <label for="humidity">Влажность (%)</label>
                <input type="number" name="humidity" id="humidity" min="0" max="100" required>
            </div>
            <div class="field">
                <label for="pressure">Давление (hPa)</label>
                <input type="number" name="pressure" id="pressure" required>
            </div>
            <div class="field">
                <label for="wind_speed">Ветер (м/с)</label>
                <input type="number" step="0.1" name="wind_speed" id="wind_speed" required>
            </div>
            <div class="field">
                <label for="description">Описание</label>
                <input type="text" name="description" id="description" required>
            </div>
            <div class="field">
                <label for="icon">Иконка</label>
                <input type="text" name="icon" id="icon" maxlength="10" placeholder="☀️" required>
            </div>
            <button type="submit">Добавить</button>
        </form>
    </div>

    <div class="card">
        <h2>Предпочтения</h2>
        <form action="{{ route('preferences.store') }}" method="post" class="flex" style="flex-direction: column">
            @csrf
            <div class="field">
                <label for="login">Имя пользователя</label>
                <input type="text" name="login" id="login" value="{{ $preferences['login'] }}" maxlength="40" required>
            </div>
            <div class="field">
                <label for="theme">Тема</label>
                <select name="theme" id="theme">
                    @foreach(['light' => 'Светлая', 'dark' => 'Тёмная', 'contrast' => 'Контрастная'] as $value => $label)
                        <option value="{{ $value }}" {{ $preferences['theme'] === $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="field">
                <label for="language">Язык</label>
                <select name="language" id="language">
                    @foreach(['ru' => 'Русский', 'en' => 'English', 'es' => 'Español'] as $value => $label)
                        <option value="{{ $value }}" {{ $preferences['language'] === $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit">Сохранить</button>
        </form>
    </div>

    <div class="card">
        <h2>Загрузка PDF</h2>
        <form action="{{ route('uploads.store') }}" method="post" enctype="multipart/form-data" class="flex" style="flex-direction: column">
            @csrf
            <div class="field">
                <label for="file">PDF (до 5 МБ)</label>
                <input type="file" name="file" id="file" accept="application/pdf" required>
            </div>
            <button type="submit">Загрузить</button>
        </form>

        @if($uploads->count())
            <h3>Загруженные файлы</h3>
            <div class="list">
                @foreach($uploads as $file)
                    <div class="card" style="margin:0;padding:12px">
                        <div style="font-weight: 600">{{ $file->original_name }}</div>
                        <div class="muted">Автор: {{ $file->uploaded_by ?? '—' }}</div>
                        <div class="flex">
                            <a class="secondary" style="padding:8px 10px; background:#e2e8f0; border-radius:8px; text-decoration:none; color:#0f172a" href="{{ route('uploads.download', $file->id) }}">Скачать</a>
                            <form action="{{ route('uploads.destroy', $file->id) }}" method="post">
                                @csrf
                                @method('delete')
                                <button type="submit" class="secondary" style="background:#fee2e2; color:#991b1b">Удалить</button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endsection
