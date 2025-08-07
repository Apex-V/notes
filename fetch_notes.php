<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'No autenticado']);
    exit;
}

require 'db.php';
header('Content-Type: application/json');

$stmt = $pdo->query("SELECT notes.id, notes.content, notes.created_at, notes.status, notes.updated_at, u1.username AS username, u2.username AS updated_by FROM notes JOIN users u1 ON notes.user_id = u1.id LEFT JOIN users u2 ON notes.updated_by = u2.id ORDER BY notes.created_at DESC");
$notes = $stmt->fetchAll();

echo json_encode($notes);
?>
