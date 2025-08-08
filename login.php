<?php
session_start();
require 'db.php';
header('Content-Type: application/json');

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

if ($username === '' || $password === '') {
    echo json_encode(['success' => false, 'message' => 'Campos incompletos']);
    exit;
}

$stmt = $pdo->prepare('SELECT id, password, role_id FROM users WHERE username = ?');
$stmt->execute([$username]);
$user = $stmt->fetch();

if ($user && password_verify($password, $user['password'])) {
    session_regenerate_id(true);
    $_SESSION['user_id'] = (int)$user['id'];
    $_SESSION['username'] = $username;
    $_SESSION['role_id'] = (int)$user['role_id'];
    echo json_encode(['success' => true, 'username' => $username, 'role_id' => (int)$user['role_id']]);
    exit;
} else {
    echo json_encode(['success' => false, 'message' => 'Credenciales incorrectas']);
}

