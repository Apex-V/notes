<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'No autenticado']);
    exit;
}

require 'db.php';
header('Content-Type: application/json');

$stmt = $pdo->query('SELECT notes.content, notes.created_at, users.username FROM notes JOIN users ON notes.user_id = users.id ORDER BY notes.created_at DESC');
$notes = $stmt->fetchAll();

echo json_encode($notes);
?>

