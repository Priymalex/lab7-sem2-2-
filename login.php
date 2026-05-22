<?php
require_once(__DIR__ . '/csrf.php');

$config = include(__DIR__ . '/db_config.php');
header('Content-Type: text/html; charset=UTF-8');

// Безопасные настройки кук сессии
session_set_cookie_params([
    'lifetime' => 3600,
    'path' => '/',
    'secure' => false,
    'httponly' => true,
    'samesite' => 'Strict'
]);

// Подключение к БД
try {
    $db = new PDO(
        "mysql:host={$config['host']};dbname={$config['dbname']};charset=utf8", 
        $config['user'], 
        $config['pass']
    );
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    error_log('DB connection error: ' . $e->getMessage());
    die('Ошибка подключения к базе данных. Пожалуйста, попробуйте позже.');
}

// Обработка выхода
if (isset($_GET['logout'])) {
    session_destroy();
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time() - 3600, '/');
    }
    header('Location: login.php');
    exit();
}

// GET запрос - показываем форму
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Авторизация</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 50px; }
        .container { max-width: 300px; margin: 0 auto; }
        form { background: #f9f9f9; padding: 20px; border-radius: 10px; }
        input { width: 100%; padding: 8px; margin: 10px 0; box-sizing: border-box; }
        button { width: 100%; padding: 10px; background: #4CAF50; color: white; border: none; cursor: pointer; border-radius: 5px; }
        .error { color: red; margin: 10px 0; text-align: center; }
        .register-link { text-align: center; margin-top: 15px; }
        .register-link a { color: #008CBA; text-decoration: none; }
    </style>
</head>
<body>
    <div class="container">
        <form action="" method="post">
            <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
            <h2 style="text-align: center;">Вход в систему</h2>
            <input name="login" placeholder="Логин" required>
            <input name="pass" type="password" placeholder="Пароль" required>
            <button type="submit">Войти</button>
            <?php if (isset($_GET['error'])): ?>
                <div class="error"><?= htmlspecialchars($_GET['error'], ENT_QUOTES, 'UTF-8') ?></div>
            <?php endif; ?>
        </form>
        <div class="register-link">
            <a href="index.php">📝 Нет аккаунта? Заполните форму регистрации</a>
        </div>
    </div>
</body>
</html>
<?php
} 
// POST запрос - обработка входа
else {
    // Проверка CSRF
    if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
        die('Ошибка безопасности: CSRF атака обнаружена!');
    }
    
    $login = trim($_POST['login']);
    $pass = trim($_POST['pass']);
    
    // Защита от брутфорса
    $max_attempts = 5;
    $lockout_time = 900; // 15 минут
    
    if (!isset($_SESSION['login_attempts'])) {
        $_SESSION['login_attempts'] = 0;
        $_SESSION['last_attempt'] = time();
    }
    
    if ($_SESSION['login_attempts'] >= $max_attempts && 
        (time() - $_SESSION['last_attempt']) < $lockout_time) {
        die('Слишком много попыток входа. Попробуйте через 15 минут.');
    }
    
    try {
        $stmt = $db->prepare("SELECT login, pass, request_id FROM UserInfo WHERE login = ?");
        $stmt->execute([$login]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user && $user['pass'] === md5($pass)) {
            // Успешный вход
            $_SESSION['login_attempts'] = 0;
            $_SESSION['login'] = $user['login'];
            $_SESSION['request_id'] = $user['request_id'];
            
            // Регенерация ID сессии
            session_regenerate_id(true);
            
            header('Location: ./');
            exit();
        } else {
            // Неудачная попытка
            $_SESSION['login_attempts']++;
            $_SESSION['last_attempt'] = time();
            header('Location: login.php?error=Неверный логин или пароль');
            exit();
        }
    } catch (PDOException $e) {
        error_log('Login error: ' . $e->getMessage());
        header('Location: login.php?error=Произошла ошибка, попробуйте позже');
        exit();
    }
}
?>