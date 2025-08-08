<?php
session_start();
require 'db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'No autenticado']);
    exit;
}

// Verificación de rol admin (role_id = 1)
$me = $pdo->prepare('SELECT role_id FROM users WHERE id = ?');
$me->execute([$_SESSION['user_id']]);
if ((int)$me->fetchColumn() !== 1) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit;
}

$noteId = intval($_POST['id'] ?? 0);
$status = $_POST['status'] ?? '';

if (!$noteId || !in_array($status, ['Pending', 'Completed'])) {
    echo json_encode(['success' => false, 'message' => 'Datos inválidos']);
    exit;
}

$stmt = $pdo->prepare('UPDATE notes SET status = ?, updated_by = ?, updated_at = NOW() WHERE id = ?');
$stmt->execute([$status, $_SESSION['user_id'], $noteId]);

echo json_encode(['success' => true]);
