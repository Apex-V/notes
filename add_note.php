<?php
session_start();
require 'db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'No autenticado']);
    exit;
}

if (($_SESSION['role_id'] ?? 0) !== 1) {
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit;
}

$content = trim($_POST['content'] ?? '');
if ($content === '') {
    echo json_encode(['success' => false, 'message' => 'Contenido vacío']);
    exit;
}

$stmt = $pdo->prepare('INSERT INTO notes (user_id, content, status) VALUES (?, ?, "Pending")');
$stmt->execute([$_SESSION['user_id'], $content]);

echo json_encode(['success' => true]);
?>

