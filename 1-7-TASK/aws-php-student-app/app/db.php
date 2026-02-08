<?php
// app/db.php

$host = getenv('DB_HOST') ?: 'db';
$db   = getenv('DB_NAME') ?: 'task1_db';
$user = getenv('DB_USER') ?: 'root';
$pass = getenv('DB_PASS') ?: 'rootpassword';
$port = getenv('DB_PORT') ?: '3306';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conn = new mysqli($host, $user, $pass, $db, (int)$port);
    $conn->set_charset("utf8mb4");
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => "DB connection failed", "details" => $e->getMessage()]);
    exit;
}
?>
