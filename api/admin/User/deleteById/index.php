<?php
header('Content-Type: application/json; charset=utf-8');

// CORS
require_once(__DIR__ . './../../../cors.php');

// DB connection
require_once(__DIR__ . './../../../Database.php');

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $database = Database::getInstance();
    $pdo = $database->getConnection();
} catch (\PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error DB connection']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
  //  JSON from POST 
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);

    if (!$data || !isset($data['id'])) {  ///
    http_response_code(400);
    echo json_encode(['error' => 'Invalid request data']);
    exit;
    }

    $id = $data['id'];

    // check id
    try {
        $stmt = $pdo->prepare("SELECT id, name, password, role FROM users WHERE id = :id");
        $stmt->execute(['id' => $id]);
    }catch (\PDOException $e) {
        echo json_encode(['error' => 'Ошибка БД'.$e->getMessage()]);
        exit;
    }
    $user = $stmt->fetch();
    // Если пользователь не найден, прерываем выполнение
    if (!$user) {
        http_response_code(200);
        echo json_encode(['error' => 'User not found']);
        exit;
    }

    ////last_visit
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = :id");
    $stmt->execute([
            'id' => $id,
        ]);
    
    http_response_code(200); // OK
    echo json_encode(['message' => 'USER DELETED']);
}