<?php
session_start();
require 'db.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'No autenticado']);
    exit;
}

$sql = 'SELECT n.id, n.content, n.status, n.created_at, u.username
        FROM notes n
        JOIN users u ON u.id = n.created_by
        ORDER BY n.id DESC';
$notes = $pdo->query($sql)->fetchAll();

echo json_encode(['success' => true, 'notes' => $notes]);
