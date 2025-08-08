<?php
// admin_users.php
session_start();
require 'db.php';

// Verifica sesión y que sea admin
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$stmt = $pdo->prepare('SELECT id, username, role_id FROM users WHERE id = ?');
$stmt->execute([$_SESSION['user_id']]);
$me = $stmt->fetch();

if (!$me || (int)$me['role_id'] !== 1) {
    header('HTTP/1.1 403 Forbidden');
    echo 'Acceso denegado. Se requiere rol admin.';
    exit;
}
?>
<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <title>Administrador de Usuarios</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="style.css" rel="stylesheet">
</head>

<body class="users-page">
    <div class="users-container" id="usersApp">
        <header class="users-header">
            <h1>Administrar Usuarios</h1>
            <div class="users-header-right">
                <span class="users-me">Conectado: <?php echo htmlspecialchars($me['username']); ?> (admin)</span>
                <a class="users-btn ghost" href="index.php">Notas</a>
                <a class="users-btn danger ghost" href="logout.php">Salir</a>
            </div>
        </header>

        <section class="users-card">
            <h2 id="formTitle">Crear usuario</h2>
            <form id="userForm" autocomplete="off">
                <input type="hidden" id="userId">
                <div class="users-grid">
                    <div class="users-field">
                        <label for="username">Usuario</label>
                        <input id="username" name="username" required minlength="3" maxlength="32" pattern="[A-Za-z0-9_-]{3,32}" placeholder="ej. recepcionista">
                        <small>Solo letras, números, guion y guion bajo</small>
                    </div>
                    <div class="users-field">
                        <label for="password">Contraseña</label>
                        <input id="password" name="password" type="password" minlength="6" placeholder="Mínimo 6 caracteres">
                        <small>Deja vacío al editar si no deseas cambiarla</small>
                    </div>
                    <div class="users-field">
                        <label for="role">Rol</label>
                        <select id="role" name="role">
                            <option value="2">Recepcionista</option>
                            <option value="1">Administrador</option>
                        </select>
                    </div>
                </div>
                <div class="users-actions">
                    <button type="submit" class="users-btn primary" id="submitBtn">Crear</button>
                    <button type="button" class="users-btn" id="resetBtn">Cancelar</button>
                </div>
            </form>
            <div id="usersMsg" class="users-msg" hidden></div>
        </section>

        <section class="users-card">
            <div class="users-list-header">
                <h2>Usuarios</h2>
                <div class="users-tools">
                    <input id="searchUser" placeholder="Buscar por usuario…">
                    <button class="users-btn" id="refreshBtn">Recargar</button>
                </div>
            </div>
            <div class="users-table-wrap">
                <table class="users-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Usuario</th>
                            <th>Rol</th>
                            <th>Creado</th>
                            <th class="col-actions">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="usersTbody">
                        <tr>
                            <td colspan="5" class="empty">Cargando…</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="users-pagination">
                <button class="users-btn" id="prevPage">« Anterior</button>
                <span id="pageInfo">Página 1</span>
                <button class="users-btn" id="nextPage">Siguiente »</button>
            </div>
        </section>
    </div>

    <script src="script.js" defer></script>
</body>

</html>