<?php
header('Content-Type: application/json; charset=utf-8');

// CORS
require_once(__DIR__ . './../../cors.php');

// DB connection
require_once(__DIR__ . './../../Database.php');

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Ramsey\UUID\Uuid; 

require("../Firebase/JWT.php");
require("../Firebase/Key.php");

include("../../const.php");

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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  //  JSON from POST 
  $json = file_get_contents('php://input');
  $data = json_decode($json, true);

  if (!$data || !isset($data['login']) || !isset($data['password'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid request data']);
    exit;
  }

  $login = $data['login'];
  $plainPassword = $data['password'];

  // check login is free
  try {
    $stmt = $pdo->prepare("SELECT id, name, password, role FROM users WHERE login = :login");
    $stmt->execute(['login' => $login]);
  }catch (\PDOException $e) {
    echo json_encode(['error' => 'Ошибка БД'.$e->getMessage()]);
    exit;
  }
  $user = $stmt->fetch();

  if (!$user || !password_verify($plainPassword, $user['password'])) {
    http_response_code(401); // Unauthorized
    echo json_encode(['error' => 'Uncorrect login / password or you is unregistered']);
    exit;
  }
  ////last_visit
  $stmt = $pdo->prepare("UPDATE users SET last_visit = CURRENT_TIMESTAMP WHERE id = :id");
  $stmt->execute(['id' => $user['id']]);

    // Генерируем JWT токен
  $payload = [
    "iss" => "http://mysite.org",
    "aud" => "http://mysite.com",
    "iat" => time(),
    "exp" => time() + 3600,
    "data" => [
      "id" => $user['id'],
      "login" => $login,
      "name" => $user['name'],
      "role" => $user['role'],
    ]
  ];
  $jwt = JWT::encode($payload, $key,'HS256');

    // send token to client
    http_response_code(200); // OK
    echo json_encode(['token_data' => $payload,'token' => $jwt]);
}
?>
