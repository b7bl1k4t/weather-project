<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Weather' }}</title>
    <style>
        :root {
            --bg: #f7fafc;
            --card: #ffffff;
            --border: #e2e8f0;
            --accent: #2563eb;
        }
        body {
            margin: 0;
            padding: 0;
            background: var(--bg);
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            color: #0f172a;
        }
        header {
            background: #0f172a;
            color: #fff;
            padding: 16px 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        nav a {
            color: #cbd5e1;
            text-decoration: none;
            margin-left: 16px;
            font-weight: 600;
        }
        nav a.active, nav a:hover {
            color: #fff;
        }
        .container {
            max-width: 1100px;
            margin: 0 auto;
            padding: 24px;
        }
        .card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 16px;
            box-shadow: 0 10px 35px rgba(15, 23, 42, 0.05);
        }
        h1, h2, h3 {
            margin: 0 0 12px 0;
        }
        form .field {
            display: flex;
            flex-direction: column;
            margin-bottom: 12px;
        }
        label {
            font-size: 0.95rem;
            color: #334155;
            margin-bottom: 4px;
        }
        input, select, button {
            font-size: 1rem;
        }
        input, select {
            padding: 10px 12px;
            border-radius: 8px;
            border: 1px solid var(--border);
        }
        button {
            background: var(--accent);
            color: #fff;
            border: none;
            padding: 10px 14px;
            border-radius: 10px;
            cursor: pointer;
            font-weight: 600;
        }
        button.secondary {
            background: #e2e8f0;
            color: #0f172a;
        }
        .flash {
            padding: 12px 14px;
            border-radius: 10px;
            margin-bottom: 16px;
            border: 1px solid #bbf7d0;
            background: #ecfdf3;
            color: #14532d;
        }
        .list {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 12px;
        }
        .muted {
            color: #475569;
            font-size: 0.95rem;
        }
        .flex {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }
        .chart {
            max-width: 100%;
            height: auto;
            border: 1px solid var(--border);
            border-radius: 10px;
        }
    </style>
</head>
<body>
    <header>
        <div><strong>🌤️ Weather</strong></div>
        <nav>
            <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">Главная</a>
            <a href="{{ route('stats') }}" class="{{ request()->routeIs('stats') ? 'active' : '' }}">Статистика</a>
        </nav>
    </header>
    <div class="container">
        @if(session('status'))
            <div class="flash">{{ session('status') }}</div>
        @endif
        @yield('content')
    </div>
</body>
</html>
