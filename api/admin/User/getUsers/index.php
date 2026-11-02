<?php
header('Content-Type: application/json; charset=utf-8');

// CORS
require_once(__DIR__ . './../../../cors.php');

// Подключаем класс Database для централизованного подключения к БД
require_once(__DIR__ . './../../../Database.php');

// Получаем подключение к базе данных
try {
    $database = Database::getInstance();
    $pdo = $database->getConnection();
} catch (\PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error DB connection']);
    exit;
}

try {
    // Получаем все данные из таблицы users, кроме поля password
    $stmt = $pdo->query("SELECT id, login, name, email, role, signed, last_visit FROM users");
    $arr = $stmt->fetchAll();
    
    echo json_encode($arr);
} catch (\PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Ошибка выполнения запроса: ' . $e->getMessage()]);
    exit;
}
