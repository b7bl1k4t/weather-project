<!doctype html>
<html lang="{{ $preferences['language'] ?? 'ru' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Weather' }}</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body class="theme-{{ $preferences['theme'] ?? 'light' }}" data-language="{{ $preferences['language'] ?? 'ru' }}">
    <nav class="main-nav">
        <div class="nav-container">
            <a class="nav-logo" href="{{ route('home') }}" style="text-decoration:none;">
                <span>🌤️ Weather</span>
            </a>
            <ul class="nav-menu">
                <li><a href="{{ route('home') }}" class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}">{{ $strings['nav_home'] ?? 'Главная' }}</a></li>
                <li><a href="{{ route('about') }}" class="nav-link {{ request()->routeIs('about') ? 'active' : '' }}">{{ $strings['nav_about'] ?? 'О проекте' }}</a></li>
                <li><a href="{{ route('stats') }}" class="nav-link {{ request()->routeIs('stats') ? 'active' : '' }}">{{ $strings['nav_stats'] ?? 'Статистика' }}</a></li>
                <li><a href="{{ route('admin') }}" class="nav-link {{ request()->routeIs('admin') ? 'active' : '' }}">{{ $strings['nav_admin'] ?? 'Админка' }}</a></li>
            </ul>
        </div>
    </nav>

    <div class="container">
        @if(session('status'))
            <div class="flash-message">{{ session('status') }}</div>
        @endif
        @yield('content')
    </div>

    <div class="chart-modal" data-chart-modal>
        <img src="" alt="chart">
    </div>
    <script src="/js/app.js"></script>
</body>
</html>
