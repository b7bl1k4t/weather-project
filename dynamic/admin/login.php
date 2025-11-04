<?php
session_start();

// Подключение к БД
$host = 'postgres';
$dbname = 'weather_db';
$username = 'weather_user';
$password = 'weather_pass';

try {
    $pdo = new PDO("pgsql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Ошибка подключения: " . $e->getMessage());
}

// Обработка формы входа
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username'])) {
    $user = $_POST['username'];
    $pass = $_POST['password'];
    
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$user]);
    $user_data = $stmt->fetch();
    
    if ($user_data && password_verify($pass, $user_data['password'])) {
        $_SESSION['admin'] = true;
        header('Location: index.php');
        exit;
    } else {
        $error = "Неверный логин или пароль!";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Вход в админку</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔐 Вход в админку</h1>
        </div>
        
        <div class="weather-card">
            <form method="POST">
                <div class="form-group">
                    <label>Логин:</label>
                    <input type="text" name="username" required>
                </div>
                <div class="form-group">
                    <label>Пароль:</label>
                    <input type="password" name="password" required>
                </div>
                <?php if (isset($error)): ?>
                    <p style="color: red;"><?php echo $error; ?></p>
                <?php endif; ?>
                <button type="submit" class="btn">Войти</button>
            </form>
            <p>Логин: admin, Пароль: password</p>
        </div>
    </div>
</body>
</html>