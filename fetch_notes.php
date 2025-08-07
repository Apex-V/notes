<?php
require 'db.php';
header('Content-Type: application/json');

$stmt = $pdo->query('SELECT notes.content, notes.created_at, users.username FROM notes JOIN users ON notes.user_id = users.id ORDER BY notes.created_at DESC');
$notes = $stmt->fetchAll();

echo json_encode($notes);
?>

