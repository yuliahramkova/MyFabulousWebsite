<?php
// конфигурационный файл для авторизации

$log_file = 'logs/auth.log';

session_start(); 
// Когда пользователь заходит на сайт, сервер создает для него уникальный номер (ID сессии) и сохраняет этот ID в браузере 
// в виде cookie.

$users = [
    'admin' => [
        'id' => 1,
        'name' => 'Администратор',
        'password_hash' => '$2y$12$VsjTbnp/OFZM/1zM.zRNSuLPgz9wc3moUbNfekK1HLN3fJSvinEwO', // admin123
        'role' => 'admin'
    ],
    'user' => [
        'id' => 2,
        'name' => 'Обычный пользователь',
        'password_hash' => '$2y$12$CetwDvo8GpMAxKcrX1YreeZHJZ.FCVNZ8vzk3ZclGtVAnYgRYFbA2', // user123
        'role' => 'user'
    ]   
];

// Функция логирования
function writeLog($login, $action, $additional_info = '') {
    global $log_file;  
    $time = date('Y-m-d H:i:s');
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    
    $log_entry = sprintf(
        "%s | ip=%s | login=%s | action=%s%s\n",
        $time,
        $ip,
        $login,
        $action,
        $additional_info ? " | info=" . $additional_info : ''
    );
}

// Проверка авторизации
function isAuthenticated() {
    return isset($_SESSION['user_id']);
}

// Проверка роли администратора
function isAdmin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

// Перенаправление пользователя на другую страницу
function redirect($url, $error = null) {
    if ($error) {
        $_SESSION['error'] = $error;
    }
    header("Location: $url"); // Говорим браузеру: "Перейди на страницу $url"
    exit;
}

// Получение текущего пользователя
function getCurrentUser() {
    if (isAuthenticated()) {
        return [
            'id' => $_SESSION['user_id'],
            'login' => $_SESSION['user_login'],
            'name' => $_SESSION['user_name'],
            'role' => $_SESSION['user_role']
        ];
    }
    return null;
}

// $_SERVER - это суперглобальный массив, в котором PHP хранит информацию о сервере и запросе
// ['REMOTE_ADDR'] - это ключ, который хранит IP-адрес пользователя (кто делает запрос)
// ?? - это оператор "null coalescing" (произносится как "или если нет")
// 'unknown' - значение по умолчанию
// sprintf() - форматирование строки
// file_put_contents Записывает в файл
// isset() - проверяет, существует ли переменная и не равна ли она null.
// $hash = password_hash("admin123", PASSWORD_DEFAULT); - хэширование Bcrypt 

?>