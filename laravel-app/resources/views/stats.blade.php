@php
    $title = 'Статистика';
    $cacheBuster = time();
@endphp
@extends('layouts.app')

@section('content')
    <div class="card">
        <h1>Статистика и графики</h1>
        @if($dbError ?? false)
            <p class="muted">Ошибка БД: {{ $dbError }}</p>
        @else
            <div class="flex">
                <div><strong>{{ $count }}</strong><div class="muted">Записей</div></div>
                <div><strong>{{ $lastRecord ?? '—' }}</strong><div class="muted">Последняя запись</div></div>
                @if(($autofilled ?? 0) > 0)
                    <div class="muted">Для графиков добавлено {{ $autofilled }} демо-записей.</div>
                @endif
            </div>
        @endif
    </div>

    <div class="card">
        <h2>Графики</h2>
        <div class="flex">
            <div style="flex:1; min-width:260px">
                <h3>Сутки</h3>
                <img class="chart" src="{{ url('/api/charts/daily') }}?v={{ $cacheBuster }}" alt="Температура за сутки">
            </div>
            <div style="flex:1; min-width:260px">
                <h3>Неделя</h3>
                <img class="chart" src="{{ url('/api/charts/weekly') }}?v={{ $cacheBuster }}" alt="Средняя температура за неделю">
            </div>
            <div style="flex:1; min-width:260px">
                <h3>Месяц</h3>
                <img class="chart" src="{{ url('/api/charts/monthly') }}?v={{ $cacheBuster }}" alt="Условия за месяц">
            </div>
        </div>
    </div>

    <div class="card">
        <h2>Управление фикстурами</h2>
        <form action="{{ route('fixtures.fill') }}" method="post" class="flex">
            @csrf
            <input type="hidden" name="count" value="60">
            <input type="hidden" name="minimum" value="50">
            <button type="submit">Добавить недостающие</button>
        </form>
        <form action="{{ route('fixtures.reset') }}" method="post" class="flex" style="margin-top:8px">
            @csrf
            <input type="hidden" name="count" value="80">
            <button type="submit" class="secondary">Пересоздать таблицу</button>
        </form>
    </div>
@endsection
