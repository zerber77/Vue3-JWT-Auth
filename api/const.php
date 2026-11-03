<?php
date_default_timezone_set('UTC');

// Надежная загрузка переменных окружения из .env файла
$envFile = __DIR__ . '/.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        // Пропускаем комментарии
        if (strpos(trim($line), '#') === 0) {
            continue;
        }
        
        // Ищем первое вхождение '='
        $pos = strpos($line, '=');
        if ($pos !== false) {
            $key = trim(substr($line, 0, $pos));
            $value = trim(substr($line, $pos + 1));
            
            // Убираем окружающие кавычки (двойные или одинарные), если они есть
            if ((strpos($value, '"') === 0 && strrpos($value, '"') === strlen($value) - 1) ||
                (strpos($value, "'") === 0 && strrpos($value, "'") === strlen($value) - 1)) {
                $value = substr($value, 1, -1);
            }
            
            // Опционально: убираем слово export, если оно присутствует (export KEY=VALUE)
            $key = str_replace('export ', '', $key);
            
            $_ENV[$key] = $value;
        }
    }
}

// Получаем значения из $_ENV с резервными значениями по умолчанию
$db = $_ENV['DB_HOST'] ?? 'localhost';
$dbname = $_ENV['DB_NAME'] ?? 'TestConstructor';
$dbuser = $_ENV['DB_USER'] ?? 'root';
$dbpasswd = $_ENV['DB_PASSWORD'] ?? '';

$key = $_ENV['JWT_SECRET_KEY'] ?? 'fallback-key'; 