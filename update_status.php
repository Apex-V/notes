<?php
session_start();
require 'db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'No autenticado']);
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
?>
