<?php
session_start();

// Обработка выхода
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: login.php');
    exit;
}

if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Админка</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <nav class="main-nav">
        <div class="nav-container">
            <div class="nav-logo"><span>🌤️ Погода - Админка</span></div>
            <ul class="nav-menu">
                <li><a href="/index.html" class="nav-link">Главная</a></li>
                <li><a href="/about.html" class="nav-link">О проекте</a></li>
                <li><a href="/index.php" class="nav-link">Динамика</a></li>
                <li><a href="index.php" class="nav-link active">Админка</a></li>
                <li><a href="?logout=1" class="nav-link">Выйти</a></li>
            </ul>
        </div>
    </nav>

    <div class="container">
        <div class="header">
            <h1>⚙️ Панель администратора</h1>
            <p>Добро пожаловать, администратор!</p>
        </div>

        <div class="weather-card">
            <h2>Управление данными</h2>
            <a href="/index.php" class="btn">Управление погодой</a>
        </div>

        <div class="weather-card">
            <h2>Статистика</h2>
            <p>Записей в базе: 
                <?php
                $host = 'postgres';
                $dbname = 'weather_db';
                $username = 'weather_user';
                $password = 'weather_pass';
                $pdo = new PDO("pgsql:host=$host;dbname=$dbname", $username, $password);
                $count = $pdo->query("SELECT COUNT(*) FROM weather_data")->fetchColumn();
                echo $count;
                ?>
            </p>
        </div>

        <div class="weather-card">
            <h2>Пользователи API</h2>
            <div id="user-list">Загрузка списка...</div>
        </div>

        <!-- КНОПКА ВЫХОДА -->
        <div class="admin-panel" style="text-align: center; margin-top: 30px;">
            <a href="?logout=1" class="btn" style="background: #e74c3c;">🚪 Выйти из админки</a>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const container = document.getElementById('user-list');

            function renderUsers(users) {
                container.innerHTML = '';
                if (!users.length) {
                    container.textContent = 'Пользователи не найдены.';
                    return;
                }

                users.forEach(user => {
                    const row = document.createElement('div');
                    row.className = 'user-row';
                    row.style.display = 'flex';
                    row.style.justifyContent = 'space-between';
                    row.style.padding = '8px 0';
                    row.style.borderBottom = '1px solid rgba(0,0,0,0.05)';

                    const name = document.createElement('div');
                    name.textContent = user.username;
                    name.style.fontWeight = '600';

                    const meta = document.createElement('div');
                    meta.style.fontSize = '0.9em';
                    meta.style.color = '#666';
                    const email = user.email || 'email не указан';
                    meta.textContent = `${email} • ${user.created_at}`;

                    row.appendChild(name);
                    row.appendChild(meta);
                    container.appendChild(row);
                });
            }

            fetch('/api/users.php?limit=50', { headers: { 'Accept': 'application/json' } })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`Ошибка API: ${response.status}`);
                    }
                    return response.json();
                })
                .then(payload => {
                    renderUsers(Array.isArray(payload.data) ? payload.data : []);
                })
                .catch(error => {
                    console.error(error);
                    container.textContent = 'Не удалось загрузить пользователей через API.';
                });
        });
    </script>
</body>
</html>
