<?php
session_start();
require 'db.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'No autenticado']);
    exit;
}

// Acepta JSON o FormData
$ct  = $_SERVER['CONTENT_TYPE'] ?? '';
$raw = file_get_contents('php://input') ?: '';
if (stripos($ct, 'application/json') !== false) {
    $data = json_decode($raw, true) ?: [];
} else {
    $data = $_POST;
}

$content = trim($data['content'] ?? '');
if ($content === '') {
    echo json_encode(['success' => false, 'message' => 'Contenido vacío']);
    exit;
}

$stmt = $pdo->prepare('INSERT INTO notes (content, created_by, status, created_at) VALUES (?, ?, "Pending", NOW())');
$stmt->execute([$content, $_SESSION['user_id']]);

echo json_encode(['success' => true, 'id' => (int)$pdo->lastInsertId()]);
