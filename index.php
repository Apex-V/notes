<?php
session_start();
require 'db.php';

//Ensure default users exist with hashed passwords
// $defaults = [
//     ['admin', 'admin123', 1],
//     ['recepcionista', 'recep456', 2]
// ];

// foreach ($defaults as $user) {
//     [$name, $pass, $role] = $user;
//     $stmt = $pdo->prepare('SELECT id FROM users WHERE username = ?');
//     $stmt->execute([$name]);
//     if (!$stmt->fetch()) {
//         $hash = password_hash($pass, PASSWORD_BCRYPT);
//         $ins = $pdo->prepare('INSERT INTO users (username, password, role_id) VALUES (?, ?, ?)');
//         $ins->execute([$name, $hash, $role]);
//     }
// }

$loggedUser = $_SESSION['username'] ?? null;
$loggedRole = $_SESSION['role_id'] ?? null;
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Notas de Todos</title>
    <link rel="stylesheet" href="style.css" />
</head>

<body>
    <div class="container">
        <h1>📝 Notas Compartidas</h1>

        <div id="loginSection" <?php if ($loggedUser) echo 'style="display:none;"'; ?>>
            <input type="text" id="loginUsername" placeholder="Usuario" />
            <input type="password" id="loginPassword" placeholder="Contraseña" />
            <button onclick="login()">🔐 Iniciar sesión</button>
            <!-- <p>¿No tienes cuenta? <a href="#" onclick="showRegister()">Regístrate</a></p> -->
        </div>

        <div id="registerSection" style="display: none;">
            <input type="text" id="registerUsername" placeholder="Nuevo usuario" />
            <input type="password" id="registerPassword" placeholder="Contraseña" />
            <button onclick="register()">📝 Registrarse</button>
            <p>¿Ya tienes cuenta? <a href="#" onclick="showLogin()">Inicia sesión</a></p>
        </div>

        <div id="noteSection" <?php if (!$loggedUser) echo 'style="display:none;"'; ?>>
            <p>👤 Sesión iniciada como <span id="userDisplay"><?php echo htmlspecialchars($loggedUser ?? '', ENT_QUOTES, 'UTF-8'); ?></span>
                <a id="manageUsersLink" href="admin_users.php" style="display:none;">Gestionar usuarios</a>
                <button onclick="logout()">Salir</button>
            </p>
            <textarea id="noteInput" placeholder="Escribe una nota..." style="display:none;"></textarea>
            <button id="addNoteBtn" onclick="addNote()" style="display:none;">📝 Publicar Nota</button>
        </div>

        <h2 <?= !$loggedUser ? 'style="display:none;"' : '' ?>>📚 Todas las notas</h2>
        <div id="notesList" <?= !$loggedUser ? 'style="display:none;"' : '' ?>></div>
    </div>

    <script>
        const loggedUser = <?php echo json_encode($_SESSION['username'] ?? null); ?>;
        const loggedRole = <?php echo json_encode($_SESSION['role_id'] ?? null); ?>; // 1=admin, 2=recep
    </script>
    <script src="script.js">

    </script>
</body>

</html>