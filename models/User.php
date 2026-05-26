<?php
class User {
    private $dataFile;
    private $users = [];
    
    public function __construct() {
        $this->dataFile = __DIR__ . '/../data/users.json';
        $this->loadUsers();
    }
    
    private function loadUsers() {
        if (file_exists($this->dataFile)) {
            $json = file_get_contents($this->dataFile);
            $this->users = json_decode($json, true) ?: [];
        }
    }
    
    private function saveUsers() {
        file_put_contents($this->dataFile, json_encode($this->users, JSON_UNESCAPED_UNICODE));
    }
    
    private function getNextId() {
        if (empty($this->users)) return 1;
        $ids = array_column($this->users, 'id');
        return max($ids) + 1;
    }
    
    // POST /register - регистрация
    public function register($name, $email, $password) {
        // проверка на повторную регистрацию (уникальность email)
        foreach ($this->users as $user) {
            if ($user['email'] === $email) {
                return ['status' => 'error', 'message' => 'Пользователь с таким email уже существует'];
            }
        }
        
        // проверка пустых полей
        if (empty($name) || empty($email) || empty($password)) {
            return ['status' => 'error', 'message' => 'Все поля обязательны для заполнения'];
        }
        
        // проверка корректности email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['status' => 'error', 'message' => 'Неверный формат email'];
        }
        
        // проверка длины пароля
        if (strlen($password) < 4) {
            return ['status' => 'error', 'message' => 'Пароль должен содержать минимум 4 символа'];
        }
        
        $newUser = [
            'id' => $this->getNextId(),
            'name' => $name,
            'email' => $email,
            'password_hash' => password_hash($password, PASSWORD_DEFAULT)
        ];
        
        $this->users[] = $newUser;
        $this->saveUsers();
        
        return [
            'status' => 'success',
            'message' => 'Пользователь зарегистрирован',
            'user' => [
                'id' => $newUser['id'],
                'name' => $newUser['name'],
                'email' => $newUser['email']
            ]
        ];
    }
    
    // POST /login - авторизация
    public function login($email, $password) {
        if (empty($email) || empty($password)) {
            return ['status' => 'error', 'message' => 'Email и пароль обязательны'];
        }
        
        foreach ($this->users as $user) {
            if ($user['email'] === $email) {
                if (password_verify($password, $user['password_hash'])) {
                    return [
                        'status' => 'success',
                        'message' => 'Авторизация успешна',
                        'user' => [
                            'id' => $user['id'],
                            'name' => $user['name'],
                            'email' => $user['email']
                        ]
                    ];
                } else {
                    return ['status' => 'error', 'message' => 'Неверный пароль'];
                }
            }
        }
        
        return ['status' => 'error', 'message' => 'Пользователь не найден'];
    }
    
    // GET /users - список всех пользователей
    public function getAll() {
        $users = array_map(function($user) {
            return [
                'id' => $user['id'],
                'name' => $user['name'],
                'email' => $user['email']
            ];
        }, $this->users);
        
        return ['status' => 'success', 'data' => $users];
    }
    
    // GET /users/{id} - один пользователь
    public function getById($id) {
        foreach ($this->users as $user) {
            if ($user['id'] == $id) {
                return [
                    'status' => 'success',
                    'data' => [
                        'id' => $user['id'],
                        'name' => $user['name'],
                        'email' => $user['email']
                    ]
                ];
            }
        }
        
        return ['status' => 'error', 'message' => 'Пользователь не найден'];
    }
    
    // PATCH /users/{id} - изменение пароля
    public function updatePassword($id, $newPassword) {
        if (empty($newPassword)) {
            return ['status' => 'error', 'message' => 'Новый пароль не может быть пустым'];
        }
        
        if (strlen($newPassword) < 4) {
            return ['status' => 'error', 'message' => 'Пароль должен содержать минимум 4 символа'];
        }
        
        foreach ($this->users as &$user) {
            if ($user['id'] == $id) {
                $user['password_hash'] = password_hash($newPassword, PASSWORD_DEFAULT);
                $this->saveUsers();
                return ['status' => 'success', 'message' => 'Пароль успешно изменён'];
            }
        }
        
        return ['status' => 'error', 'message' => 'Пользователь не найден'];
    }
    
    // PUT/PATCH /users/{id} - обновление данных пользователя
    public function update($id, $name, $email) {
        foreach ($this->users as &$user) {
            if ($user['id'] == $id) {
                // проверка уникальности email
                foreach ($this->users as $other) {
                    if ($other['id'] != $id && $other['email'] === $email) {
                        return ['status' => 'error', 'message' => 'Email уже используется'];
                    }
                }
                
                if (!empty($name)) {
                    $user['name'] = $name;
                }
                if (!empty($email)) {
                    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        return ['status' => 'error', 'message' => 'Неверный формат email'];
                    }
                    $user['email'] = $email;
                }
                
                $this->saveUsers();
                return [
                    'status' => 'success',
                    'message' => 'Данные пользователя обновлены',
                    'user' => [
                        'id' => $user['id'],
                        'name' => $user['name'],
                        'email' => $user['email']
                    ]
                ];
            }
        }
        
        return ['status' => 'error', 'message' => 'Пользователь не найден'];
    }
    
    // DELETE /users/{id} - удаление пользователя
    public function delete($id) {
        foreach ($this->users as $key => $user) {
            if ($user['id'] == $id) {
                unset($this->users[$key]);
                $this->users = array_values($this->users);
                $this->saveUsers();
                return ['status' => 'success', 'message' => 'Пользователь удалён'];
            }
        }
        
        return ['status' => 'error', 'message' => 'Пользователь не найден'];
    }
    // file_get_contents() Функция, которая читает весь файл в строку.

    // $result = $value ?: $default;
    // Эквивалентно:
    // $result = $value ? $value : $default;

// JSON_UNESCAPED_UNICODE — Это флаг (константа) для функций json_encode() и json_decode(). По умолчанию PHP преобразует русские буквы в \u0442\u0430\u043a\u0438\u0435 коды.
// PASSWORD_DEFAULT — Это константа, которая указывает PHP использовать лучший доступный алгоритм хэширования паролей (на текущий момент — bcrypt).


}