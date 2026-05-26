<?php
require_once __DIR__ . '/../models/User.php';

class UserController {
    private $userModel;
    
    public function __construct() {
        $this->userModel = new User();
    }
    
    // отправка JSON-ответа
    private function sendJson($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    // POST /register
    public function register() {
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!$input) {
            $this->sendJson(['status' => 'error', 'message' => 'Неверный формат JSON'], 400);
        }
        
        $result = $this->userModel->register(
            $input['name'] ?? '',
            $input['email'] ?? '',
            $input['password'] ?? ''
        );
        
        $code = ($result['status'] === 'success') ? 200 : 400;
        $this->sendJson($result, $code);
    }
    
    // POST /login
    public function login() {
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!$input) {
            $this->sendJson(['status' => 'error', 'message' => 'Неверный формат JSON'], 400);
        }
        
        $result = $this->userModel->login(
            $input['email'] ?? '',
            $input['password'] ?? ''
        );
        
        $code = ($result['status'] === 'success') ? 200 : 401;
        $this->sendJson($result, $code);
    }
    
    // GET /users
    public function getAllUsers() {
        $result = $this->userModel->getAll();
        $this->sendJson($result);
    }
    
    // GET /users/{id}
    public function getUser($id) {
        $result = $this->userModel->getById($id);
        $code = ($result['status'] === 'success') ? 200 : 404;
        $this->sendJson($result, $code);
    }
    
    // PUT /users/{id} 
    public function updateUser($id) {
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!$input) {
            $this->sendJson(['status' => 'error', 'message' => 'Неверный формат JSON'], 400);
        }
        
        $result = $this->userModel->update(
            $id,
            $input['name'] ?? '',
            $input['email'] ?? ''
        );
        
        $code = ($result['status'] === 'success') ? 200 : 400;
        $this->sendJson($result, $code);
    }
    
    // PATCH /users/{id}
    public function patchUser($id) {
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!$input) {
            $this->sendJson(['status' => 'error', 'message' => 'Неверный формат JSON'], 400);
        }
        
        // если есть меняем пароль
        if (isset($input['password'])) {
            $result = $this->userModel->updatePassword($id, $input['password']);
        } 
        // если есть name/email обновляем данные
        elseif (isset($input['name']) || isset($input['email'])) {
            $result = $this->userModel->update(
                $id,
                $input['name'] ?? '',
                $input['email'] ?? ''
            );
        } else {
            $this->sendJson(['status' => 'error', 'message' => 'Нет данных для обновления'], 400);
            return;
        }
        
        $code = ($result['status'] === 'success') ? 200 : 400;
        $this->sendJson($result, $code);
    }
    
    // DELETE /users/{id}
    public function deleteUser($id) {
        $result = $this->userModel->delete($id);
        $code = ($result['status'] === 'success') ? 200 : 404;
        $this->sendJson($result, $code);
    }

// http_response_code() Это функция PHP, которая устанавливает или получает HTTP-статус код ответа.
// 200 - успех, 400 - ошибка клиента, 401 - не авторизован, 404 - не найдено

}