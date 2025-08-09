<?php
session_start();
require 'db.php';
header('Content-Type: application/json');

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

$me = $pdo->prepare('SELECT role_id FROM users WHERE id = ?');
$me->execute([$_SESSION['user_id']]);
if ((int)$me->fetchColumn() !== 1) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit;
}

$id = intval($_POST['id'] ?? 0);
if (!$id) {
    echo json_encode(['success' => false, 'message' => 'ID inválido']);
    exit;
}

$stmt = $pdo->prepare('DELETE FROM notes WHERE id = ?');
$stmt->execute([$id]);

echo json_encode(['success' => true]);
?>
