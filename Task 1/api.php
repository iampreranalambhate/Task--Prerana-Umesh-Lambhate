<?php

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
    http_response_code(200);
    exit;
}

$host = "localhost";
$user = "root";
$pass = "";         
$dbname = "task1_db";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    echo json_encode([
        "success" => false,
        "message" => "DB connection failed",
        "error" => $conn->connect_error
    ]);
    exit;
}

function getJsonBody() {
    $raw = file_get_contents("php://input");
    $data = json_decode($raw, true);
    return is_array($data) ? $data : [];
}

$action = $_GET["action"] ?? "";

if ($action === "list") {
    $sql = "SELECT id, name, email, created_at FROM students ORDER BY id DESC";
    $result = $conn->query($sql);

    $rows = [];
    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }

    echo json_encode(["success" => true, "data" => $rows]);
    exit;
}

if ($action === "add" && $_SERVER["REQUEST_METHOD"] === "POST") {
    $data = getJsonBody();
    $name = trim($data["name"] ?? "");
    $email = trim($data["email"] ?? "");

    if ($name === "" || $email === "") {
        echo json_encode(["success" => false, "message" => "Name and Email required"]);
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO students (name, email) VALUES (?, ?)");
    $stmt->bind_param("ss", $name, $email);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Student added"]);
    } else {
        echo json_encode([
            "success" => false,
            "message" => "Insert failed (maybe duplicate email)",
            "error" => $stmt->error
        ]);
    }
    $stmt->close();
    exit;
}

if ($action === "delete" && $_SERVER["REQUEST_METHOD"] === "POST") {
    $data = getJsonBody();
    $id = intval($data["id"] ?? 0);

    if ($id <= 0) {
        echo json_encode(["success" => false, "message" => "Valid ID required"]);
        exit;
    }

    $stmt = $conn->prepare("DELETE FROM students WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Deleted"]);
    } else {
        echo json_encode(["success" => false, "message" => "Delete failed", "error" => $stmt->error]);
    }
    $stmt->close();
    exit;
}

echo json_encode([
    "success" => false,
    "message" => "Invalid action. Use ?action=list | add | delete"
]);
