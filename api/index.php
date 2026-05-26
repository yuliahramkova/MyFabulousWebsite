<?php
require_once __DIR__ . '/../controllers/UserController.php';

$method = $_SERVER['REQUEST_METHOD'];
$requestUri = $_SERVER['REQUEST_URI'];

// убираем базовый путь /api/v1
$basePath = '/api/v1';
if (strpos($requestUri, $basePath) === 0) {
    $path = substr($requestUri, strlen($basePath));
} else {
    $path = $requestUri;
}

// убираем GET-параметры
$path = strtok($path, '?');

$controller = new UserController();

// Маршрутизация
// POST /register
if ($method === 'POST' && $path === '/register') {
    $controller->register();
}
// POST /login
elseif ($method === 'POST' && $path === '/login') {
    $controller->login();
}
// GET /users
elseif ($method === 'GET' && $path === '/users') {
    $controller->getAllUsers();
}
// GET /users/{id}
elseif ($method === 'GET' && preg_match('#^/users/(\d+)$#', $path, $matches)) {
    $controller->getUser($matches[1]);
}
// PUT /users/{id}
elseif ($method === 'PUT' && preg_match('#^/users/(\d+)$#', $path, $matches)) {
    $controller->updateUser($matches[1]);
}
// PATCH /users/{id}
elseif ($method === 'PATCH' && preg_match('#^/users/(\d+)$#', $path, $matches)) {
    $controller->patchUser($matches[1]);
}
// DELETE /users/{id}
elseif ($method === 'DELETE' && preg_match('#^/users/(\d+)$#', $path, $matches)) {
    $controller->deleteUser($matches[1]);
}
else {
    http_response_code(404);
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Маршрут не найден'], JSON_UNESCAPED_UNICODE);
}
// _SERVER['REQUEST_URI'] Возвращает полный URI (адрес) запроса (без домена, но с параметрами).
// strtok() — функция для разбиения строки на части (токены). Она отрезает всё, что идёт до указанного разделителя.
