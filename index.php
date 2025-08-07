<?php
session_start();
$loggedUser = $_SESSION['username'] ?? null;
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
            <p>¿No tienes cuenta? <a href="#" onclick="showRegister()">Regístrate</a></p>
        </div>

        <div id="registerSection" style="display: none;">
            <input type="text" id="registerUsername" placeholder="Nuevo usuario" />
            <input type="password" id="registerPassword" placeholder="Contraseña" />
            <button onclick="register()">📝 Registrarse</button>
            <p>¿Ya tienes cuenta? <a href="#" onclick="showLogin()">Inicia sesión</a></p>
        </div>

        <div id="noteSection" <?php if (!$loggedUser) echo 'style="display:none;"'; ?>>
            <p>👤 Sesión iniciada como <span id="userDisplay"><?php echo htmlspecialchars($loggedUser ?? '', ENT_QUOTES, 'UTF-8'); ?></span> <button onclick="logout()">Salir</button></p>
            <textarea id="noteInput" placeholder="Escribe una nota..."></textarea>
            <button onclick="addNote()">📝 Publicar Nota</button>
        </div>

        <h2>📚 Todas las notas</h2>
        <div id="notesList"></div>
    </div>

    <script>
        const loggedUser = <?php echo $loggedUser ? json_encode($loggedUser) : 'null'; ?>;
    </script>
    <script src="script.js"></script>
</body>

</html>