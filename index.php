<?php
require_once(__DIR__ . '/csrf.php');

$config = include(__DIR__ . '/db_config.php');

header('Content-Type: text/html; charset=UTF-8');

// Безопасные настройки кук сессии
session_set_cookie_params([
    'lifetime' => 3600,
    'path' => '/',
    'secure' => false,  // true при использовании HTTPS
    'httponly' => true,
    'samesite' => 'Strict'
]);

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $messages = array();
    
    // Проверка авторизации
    $isAuthorized = false;
    if (!empty($_COOKIE[session_name()])) {
        if (!empty($_SESSION['login'])) {
            $isAuthorized = true;
            $messages[] = sprintf('Вы вошли как <strong>%s</strong>. <a href="login.php?logout=1">Выйти</a>', 
                htmlspecialchars($_SESSION['login'], ENT_QUOTES, 'UTF-8'));
        }
    }
    
    // Сообщение о сохранении
    if (!empty($_COOKIE['save'])) {
        setcookie('save', '', 100000);
        $messages[] = 'Спасибо, результаты сохранены.';
        
        if (!$isAuthorized && !empty($_COOKIE['pass'])) {
            $messages[] = sprintf('Вы можете <a href="login.php">Войти</a> с логином <strong>%s</strong>
                и паролем <strong>%s</strong> для изменения данных.',
                htmlspecialchars(strip_tags($_COOKIE['login']), ENT_QUOTES, 'UTF-8'),
                htmlspecialchars(strip_tags($_COOKIE['pass']), ENT_QUOTES, 'UTF-8'));
        }
        setcookie('login', '', 100000);
        setcookie('pass', '', 100000);
    }
    
    // Чтение ошибок из кук
    $errors = array();
    $errors['FIO'] = !empty($_COOKIE['FIO_error']);
    $errors['telep'] = !empty($_COOKIE['telep_error']);
    $errors['mail'] = !empty($_COOKIE['mail_error']);
    $errors['date'] = !empty($_COOKIE['date_error']);
    $errors['sex'] = !empty($_COOKIE['sex_error']);
    $errors['language'] = !empty($_COOKIE['language_error']);
    $errors['bio'] = !empty($_COOKIE['bio_error']);
    $errors['agreement'] = !empty($_COOKIE['agreement_error']);
    
    $error_messages = array();
    
    if ($errors['FIO']) {
        $error_messages['FIO'] = isset($_COOKIE['FIO_msg']) ? htmlspecialchars($_COOKIE['FIO_msg'], ENT_QUOTES, 'UTF-8') : 'Ошибка в поле ФИО';
        setcookie('FIO_error', '', 100000);
        setcookie('FIO_msg', '', 100000);
    }
    if ($errors['telep']) {
        $error_messages['telep'] = isset($_COOKIE['telep_msg']) ? htmlspecialchars($_COOKIE['telep_msg'], ENT_QUOTES, 'UTF-8') : 'Ошибка в поле Телефон';
        setcookie('telep_error', '', 100000);
        setcookie('telep_msg', '', 100000);
    }
    if ($errors['mail']) {
        $error_messages['mail'] = isset($_COOKIE['mail_msg']) ? htmlspecialchars($_COOKIE['mail_msg'], ENT_QUOTES, 'UTF-8') : 'Ошибка в поле Email';
        setcookie('mail_error', '', 100000);
        setcookie('mail_msg', '', 100000);
    }
    if ($errors['date']) {
        $error_messages['date'] = isset($_COOKIE['date_msg']) ? htmlspecialchars($_COOKIE['date_msg'], ENT_QUOTES, 'UTF-8') : 'Ошибка в поле Дата рождения';
        setcookie('date_error', '', 100000);
        setcookie('date_msg', '', 100000);
    }
    if ($errors['sex']) {
        $error_messages['sex'] = isset($_COOKIE['sex_msg']) ? htmlspecialchars($_COOKIE['sex_msg'], ENT_QUOTES, 'UTF-8') : 'Ошибка в поле Пол';
        setcookie('sex_error', '', 100000);
        setcookie('sex_msg', '', 100000);
    }
    if ($errors['language']) {
        $error_messages['language'] = isset($_COOKIE['language_msg']) ? htmlspecialchars($_COOKIE['language_msg'], ENT_QUOTES, 'UTF-8') : 'Ошибка в поле Языки программирования';
        setcookie('language_error', '', 100000);
        setcookie('language_msg', '', 100000);
    }
    if ($errors['bio']) {
        $error_messages['bio'] = isset($_COOKIE['bio_msg']) ? htmlspecialchars($_COOKIE['bio_msg'], ENT_QUOTES, 'UTF-8') : 'Ошибка в поле Биография';
        setcookie('bio_error', '', 100000);
        setcookie('bio_msg', '', 100000);
    }
    if ($errors['agreement']) {
        $error_messages['agreement'] = isset($_COOKIE['agreement_msg']) ? htmlspecialchars($_COOKIE['agreement_msg'], ENT_QUOTES, 'UTF-8') : 'Необходимо подтвердить согласие';
        setcookie('agreement_error', '', 100000);
        setcookie('agreement_msg', '', 100000);
    }
    
    // Чтение значений из кук
    $values = array();
    $values['FIO'] = empty($_COOKIE['FIO_value']) ? '' : htmlspecialchars(strip_tags($_COOKIE['FIO_value']), ENT_QUOTES, 'UTF-8');
    $values['telep'] = empty($_COOKIE['telep_value']) ? '' : htmlspecialchars(strip_tags($_COOKIE['telep_value']), ENT_QUOTES, 'UTF-8');
    $values['mail'] = empty($_COOKIE['mail_value']) ? '' : htmlspecialchars(strip_tags($_COOKIE['mail_value']), ENT_QUOTES, 'UTF-8');
    $values['date'] = empty($_COOKIE['date_value']) ? '' : htmlspecialchars(strip_tags($_COOKIE['date_value']), ENT_QUOTES, 'UTF-8');
    $values['sex'] = empty($_COOKIE['sex_value']) ? '' : htmlspecialchars(strip_tags($_COOKIE['sex_value']), ENT_QUOTES, 'UTF-8');
    $values['language'] = empty($_COOKIE['language_value']) ? [] : explode('|', strip_tags($_COOKIE['language_value']));
    $values['bio'] = empty($_COOKIE['bio_value']) ? '' : htmlspecialchars(strip_tags($_COOKIE['bio_value']), ENT_QUOTES, 'UTF-8');
    $values['agreement'] = empty($_COOKIE['agreement_value']) ? '' : htmlspecialchars(strip_tags($_COOKIE['agreement_value']), ENT_QUOTES, 'UTF-8');
    
    // Загрузка данных из БД если авторизован
    if ($isAuthorized) {
        try {
            $db = new PDO(
                "mysql:host={$config['host']};dbname={$config['dbname']};charset=utf8", 
                $config['user'], 
                $config['pass']
            );
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            $stmt = $db->prepare("
                SELECT r.name, r.tel, r.email, r.dateborn, r.sex, r.bio, r.agree,
                    GROUP_CONCAT(l.language_name SEPARATOR '|') as LANGUAGES
                FROM Frequest r
                JOIN UserInfo u ON r.id = u.request_id
                LEFT JOIN Connect c ON r.id = c.request_id
                LEFT JOIN LANGUAGES l ON c.language_id = l.language_id
                WHERE u.login = ?
                GROUP BY r.id
            ");
            $stmt->execute([$_SESSION['login']]);
            $userData = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($userData) {
                $values['FIO'] = htmlspecialchars($userData['name'], ENT_QUOTES, 'UTF-8');
                $values['telep'] = htmlspecialchars($userData['tel'], ENT_QUOTES, 'UTF-8');
                $values['mail'] = htmlspecialchars($userData['email'], ENT_QUOTES, 'UTF-8');
                $values['date'] = htmlspecialchars($userData['dateborn'], ENT_QUOTES, 'UTF-8');
                $values['sex'] = htmlspecialchars($userData['sex'], ENT_QUOTES, 'UTF-8');
                $values['language'] = $userData['LANGUAGES'] ? explode('|', $userData['LANGUAGES']) : [];
                $values['bio'] = htmlspecialchars($userData['bio'], ENT_QUOTES, 'UTF-8');
                $values['agreement'] = $userData['agree'] ? 'on' : '';
            }
        } catch (PDOException $e) {
            error_log('Database error: ' . $e->getMessage());
            $messages[] = 'Произошла ошибка при загрузке данных. Пожалуйста, попробуйте позже.';
        }
    }
    
    include(__DIR__ . '/form.php');
}
else {
    // POST запрос - обработка формы
    // Проверка CSRF токена
    if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
        die('Ошибка безопасности: CSRF атака обнаружена!');
    }
    
    $errors = false;
    
    // ========== ВАЛИДАЦИЯ ПОЛЕЙ ==========
    
    // ФИО
    if (empty($_POST['FIO'])) {
        setcookie('FIO_error', '1', 0);
        setcookie('FIO_msg', 'ФИО обязательно для заполнения.', 0);
        $errors = true;
    } elseif (strlen($_POST['FIO']) > 150) {
        setcookie('FIO_error', '1', 0);
        setcookie('FIO_msg', 'ФИО слишком длинное (максимум 150 символов)', 0);
        $errors = true;
    } elseif (!preg_match('/^[a-zA-Zа-яёА-ЯЁ\s\-]+$/u', $_POST['FIO'])) {
        setcookie('FIO_error', '1', 0);
        setcookie('FIO_msg', 'В ФИО допустимы только буквы, пробелы и дефис', 0);
        $errors = true;
    }
    
    // Телефон
    if (empty($_POST['telep'])) {
        setcookie('telep_error', '1', 0);
        setcookie('telep_msg', 'Номер телефона обязателен для заполнения.', 0);
        $errors = true;
    } elseif (!preg_match('/^[\+\d\s\-\(\)]{6,20}$/', $_POST['telep'])) {
        setcookie('telep_error', '1', 0);
        setcookie('telep_msg', 'Телефон введён некорректно.', 0);
        $errors = true;
    }
    
    // Email
    if (empty($_POST['mail'])) {
        setcookie('mail_error', '1', 0);
        setcookie('mail_msg', 'Email обязателен для заполнения.', 0);
        $errors = true;
    } elseif (!filter_var($_POST['mail'], FILTER_VALIDATE_EMAIL)) {
        setcookie('mail_error', '1', 0);
        setcookie('mail_msg', 'Email введён неправильно.', 0);
        $errors = true;
    }
    
    // Дата
    if (empty($_POST['date'])) {
        setcookie('date_error', '1', 0);
        setcookie('date_msg', 'Дата рождения обязательна для заполнения.', 0);
        $errors = true;
    } elseif (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $_POST['date'])) {
        setcookie('date_error', '1', 0);
        setcookie('date_msg', 'Дата рождения должна быть в формате ГГГГ-ММ-ДД', 0);
        $errors = true;
    } else {
        $date_parts = explode('-', $_POST['date']);
        if (!checkdate((int)$date_parts[1], (int)$date_parts[2], (int)$date_parts[0])) {
            setcookie('date_error', '1', 0);
            setcookie('date_msg', 'Дата рождения некорректна', 0);
            $errors = true;
        }
    }
    
    // Пол
    if (empty($_POST['sex'])) {
        setcookie('sex_error', '1', 0);
        setcookie('sex_msg', 'Необходимо выбрать пол', 0);
        $errors = true;
    } elseif (!in_array($_POST['sex'], array('Male', 'Female'))) {
        setcookie('sex_error', '1', 0);
        setcookie('sex_msg', 'Выбрано недопустимое значение пола', 0);
        $errors = true;
    }
    
    // Языки
    $allowed_languages = array('PHP', 'Python', 'Java', 'JavaScript', 'C++', 'Go');
    if (empty($_POST['language'])) {
        setcookie('language_error', '1', 0);
        setcookie('language_msg', 'Выберите хотя бы один язык программирования', 0);
        $errors = true;
    } else {
        foreach ($_POST['language'] as $lang) {
            if (!in_array($lang, $allowed_languages)) {
                setcookie('language_error', '1', 0);
                setcookie('language_msg', 'Выбран недопустимый язык программирования', 0);
                $errors = true;
                break;
            }
        }
    }
    
    // Биография
    if (empty($_POST['bio'])) {
        setcookie('bio_error', '1', 0);
        setcookie('bio_msg', 'Биография обязательна для заполнения', 0);
        $errors = true;
    } elseif (strlen($_POST['bio']) > 1000) {
        setcookie('bio_error', '1', 0);
        setcookie('bio_msg', 'Биография не должна превышать 1000 символов', 0);
        $errors = true;
    }
    
    // Согласие
    if (empty($_POST['agreement']) || $_POST['agreement'] !== 'on') {
        setcookie('agreement_error', '1', 0);
        setcookie('agreement_msg', 'Необходимо подтвердить согласие с контрактом', 0);
        $errors = true;
    }
    
    // Сохранение значений в куки
    setcookie('FIO_value', $_POST['FIO'], 0);
    setcookie('telep_value', $_POST['telep'], 0);
    setcookie('mail_value', $_POST['mail'], 0);
    setcookie('date_value', $_POST['date'], 0);
    setcookie('sex_value', $_POST['sex'], 0);
    if (!empty($_POST['language'])) {
        setcookie('language_value', implode('|', $_POST['language']), 0);
    }
    setcookie('bio_value', $_POST['bio'], 0);
    setcookie('agreement_value', $_POST['agreement'], 0);
    
    if ($errors)
        {
        header('Location: index.php');
        exit();
    }
    
    // Очистка кук ошибок
    setcookie('FIO_error', '', 100000);
    setcookie('FIO_msg', '', 100000);
    setcookie('telep_error', '', 100000);
    setcookie('telep_msg', '', 100000);
    setcookie('mail_error', '', 100000);
    setcookie('mail_msg', '', 100000);
    setcookie('date_error', '', 100000);
    setcookie('date_msg', '', 100000);
    setcookie('sex_error', '', 100000);
    setcookie('sex_msg', '', 100000);
    setcookie('language_error', '', 100000);
    setcookie('language_msg', '', 100000);
    setcookie('bio_error', '', 100000);
    setcookie('bio_msg', '', 100000);
    setcookie('agreement_error', '', 100000);
    setcookie('agreement_msg', '', 100000);
    
    // Постоянное сохранение
    setcookie('FIO_value', $_POST['FIO'], time() + 365 * 24 * 60 * 60);
    setcookie('telep_value', $_POST['telep'], time() + 365 * 24 * 60 * 60);
    setcookie('mail_value', $_POST['mail'], time() + 365 * 24 * 60 * 60);
    setcookie('date_value', $_POST['date'], time() + 365 * 24 * 60 * 60);
    setcookie('sex_value', $_POST['sex'], time() + 365 * 24 * 60 * 60);
    if (!empty($_POST['language'])) {
        setcookie('language_value', implode('|', $_POST['language']), time() + 365 * 24 * 60 * 60);
    }
    setcookie('bio_value', $_POST['bio'], time() + 365 * 24 * 60 * 60);
    setcookie('agreement_value', $_POST['agreement'], time() + 365 * 24 * 60 * 60);
    
    // Сохранение в БД
    try {
        $db = new PDO(
            "mysql:host={$config['host']};dbname={$config['dbname']};charset=utf8", 
            $config['user'], 
            $config['pass']
        );
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        if (!empty($_SESSION['login'])) {
            // Обновление существующих данных
            $stmt = $db->prepare("SELECT request_id FROM UserInfo WHERE login = ?");
            $stmt->execute([$_SESSION['login']]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user) {
                $request_id = $user['request_id'];
                $agree = ($_POST['agreement'] == 'on') ? 1 : 0;
                
                $stmt = $db->prepare("UPDATE Frequest SET name=?, tel=?, email=?, dateborn=?, sex=?, bio=?, agree=? WHERE id=?");
                $stmt->execute([$_POST['FIO'], $_POST['telep'], $_POST['mail'], $_POST['date'], $_POST['sex'], $_POST['bio'], $agree, $request_id]);
                
                $stmt = $db->prepare("DELETE FROM Connect WHERE request_id = ?");
                $stmt->execute([$request_id]);
                
                $stmt = $db->prepare("INSERT INTO Connect (request_id, language_id) VALUES (?, (SELECT language_id FROM LANGUAGES WHERE language_name = ?))");
                foreach ($_POST['language'] as $lang) {
                    $stmt->execute([$request_id, $lang]);
                }
            }
        } else {
            // Новая регистрация
            $login = substr(md5(uniqid((string)rand(), true)), 0, 10);
            $pass = substr(md5(uniqid((string)rand(), true)), 0, 8);
            
            setcookie('login', $login, time() + 365 * 24 * 60 * 60);
            setcookie('pass', $pass, time() + 365 * 24 * 60 * 60);
            
            $db->beginTransaction();
            
            $agree = ($_POST['agreement'] == 'on') ? 1 : 0;
            
            $stmt = $db->prepare("INSERT INTO Frequest (name, tel, email, dateborn, sex, bio, agree) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$_POST['FIO'], $_POST['telep'], $_POST['mail'], $_POST['date'], $_POST['sex'], $_POST['bio'], $agree]);
            
            $requestId = $db->lastInsertId();
            
            $getLangId = $db->prepare("SELECT language_id FROM LANGUAGES WHERE language_name = ?");
            $insertConn = $db->prepare("INSERT INTO Connect (request_id, language_id) VALUES (?, ?)");
            foreach ($_POST['language'] as $langName) {
                $getLangId->execute([$langName]);
                $row = $getLangId->fetch(PDO::FETCH_ASSOC);
                if ($row) {
                    $insertConn->execute([$requestId, $row['language_id']]);
                }
            }
            
            $stmt = $db->prepare("INSERT INTO UserInfo (request_id, login, pass) VALUES (?, ?, ?)");
            $stmt->execute([$requestId, $login, md5($pass)]);
            
            $db->commit();
        }
    } catch (PDOException $e) {
        error_log('Database error: ' . $e->getMessage());
    }
    
    setcookie('save', '1', time() + 365 * 24 * 60 * 60);
    header('Location: ./');
    exit();
}
?>