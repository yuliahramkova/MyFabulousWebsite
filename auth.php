<?php
// auth.php - обрабатывает отправленную форму логина

require_once 'auth_config.php';

// выход
// $_GET — суперглобальный массив, который содержит параметры из URL-строки
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    if (isset($_SESSION['user_login'])) {
        writeLog($_SESSION['user_login'], 'LOGOUT');
    }
    $_SESSION = array();
    session_destroy();
    header('Location: login.php');
    exit;
}


// Получаем данные из формы (которую отправил login.php)
$login = trim($_POST['login'] ?? '');
$password = $_POST['password'] ?? '';

// поля не пустые?
if (empty($login) || empty($password)) {
    writeLog($login ?: 'empty', 'FAIL_LOGIN', 'Поля пустые');
    redirect('login.php', 'Заполните все поля!');
}

// существует ли такой логин?
if (!isset($users[$login])) {
    writeLog($login, 'FAIL_LOGIN', 'Пользователь не найден');
    redirect('login.php', 'Неверный логин или пароль');
}

// правильный ли пароль?
$user = $users[$login];
if (password_verify($password, $user['password_hash'])) {
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_login'] = $login;
    $_SESSION['user_name'] = $user['name'];
    $_SESSION['user_role'] = $user['role'];
    
    writeLog($login, 'SUCCESS_LOGIN', "Роль: {$user['role']}");
    
    redirect('index.php', 'Добро пожаловать!');
} else {
    writeLog($login, 'FAIL_LOGIN', 'Неверный пароль');
    redirect('login.php', 'Неверный логин или пароль');
}
// trim() удаляет пробелы в начале и конце строки.
?>