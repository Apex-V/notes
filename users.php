<?php
// users.php
session_start();
header('Content-Type: application/json; charset=utf-8');

require 'db.php'; // Usa tu misma conexión PDO

// --- Helpers ---
function jsonResponse($payload, int $status = 200)
{
    http_response_code($status);
    echo json_encode($payload, JSON_UNESCAPED_UNICODE);
    exit;
}

function getInputData(): array
{
    $raw = file_get_contents('php://input') ?: '';
    $ct  = $_SERVER['CONTENT_TYPE'] ?? '';
    if (stripos($ct, 'application/json') !== false) {
        $data = json_decode($raw, true);
        return is_array($data) ? $data : [];
    }
    if ($raw && strpos($raw, '=') !== false) {
        parse_str($raw, $parsed);
        return is_array($parsed) ? $parsed : [];
    }
    return $_POST ?: [];
}

function sanitizeUsername(?string $u): ?string
{
    if ($u === null) return null;
    $u = trim($u);
    if ($u === '') return null;
    if (!preg_match('/^[A-Za-z0-9_-]{3,32}$/', $u)) return null;
    return $u;
}

// --- Auth + RBAC ---
if (!isset($_SESSION['user_id'])) {
    jsonResponse(['success' => false, 'message' => 'No autenticado'], 401);
}

// Carga el role_id del usuario en sesión para permitir sólo admin
$auth = $pdo->prepare('SELECT id, role_id FROM users WHERE id = ?');
$auth->execute([$_SESSION['user_id']]);
$authUser = $auth->fetch();
if (!$authUser || (int)$authUser['role_id'] !== 1) {
    jsonResponse(['success' => false, 'message' => 'No autorizado (se requiere admin)'], 403);
}

$method = $_SERVER['REQUEST_METHOD'];
$id = isset($_GET['id']) ? (int) $_GET['id'] : null;

try {
    switch ($method) {
        // ================= READ =================
        case 'GET':
            // GET /users.php           -> lista
            // GET /users.php?id=3      -> detalle
            if ($id) {
                $stmt = $pdo->prepare("SELECT id, username, role_id, created_at FROM users WHERE id = ?");
                $stmt->execute([$id]);
                $row = $stmt->fetch();
                if (!$row) jsonResponse(['success' => false, 'message' => 'Usuario no encontrado'], 404);
                jsonResponse(['success' => true, 'data' => $row]);
            } else {
                $page  = max(1, (int)($_GET['page'] ?? 1));
                $limit = max(1, min(200, (int)($_GET['limit'] ?? 50)));
                $offset = ($page - 1) * $limit;

                $total = (int)$pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
                $stmt  = $pdo->prepare("SELECT id, username, role_id, created_at
                                        FROM users
                                        ORDER BY id DESC
                                        LIMIT :limit OFFSET :offset");
                $stmt->bindValue(':limit',  $limit,  PDO::PARAM_INT);
                $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
                $stmt->execute();
                $rows = $stmt->fetchAll();

                jsonResponse(['success' => true, 'data' => $rows, 'meta' => [
                    'page' => $page,
                    'limit' => $limit,
                    'total' => $total
                ]]);
            }
            break;

        // ================= CREATE =================
        case 'POST':
            // Body: { "username":"nuevo", "password":"secreto", "role_id":2 }
            $data = getInputData();
            $username = sanitizeUsername($data['username'] ?? null);
            $password = $data['password'] ?? null;
            $role_id  = isset($data['role_id']) ? (int)$data['role_id'] : 2;

            if (!$username || !$password) {
                jsonResponse(['success' => false, 'message' => 'username y password son obligatorios'], 400);
            }
            if (strlen($password) < 6) {
                jsonResponse(['success' => false, 'message' => 'La contraseña debe tener al menos 6 caracteres'], 400);
            }

            // Duplicado
            $exists = $pdo->prepare("SELECT id FROM users WHERE username = ?");
            $exists->execute([$username]);
            if ($exists->fetch()) {
                jsonResponse(['success' => false, 'message' => 'El nombre de usuario ya existe'], 409);
            }

            $hash = password_hash($password, PASSWORD_BCRYPT);
            $ins = $pdo->prepare("INSERT INTO users (username, password, role_id) VALUES (?, ?, ?)");
            $ins->execute([$username, $hash, $role_id]);
            jsonResponse(['success' => true, 'message' => 'Usuario creado', 'id' => (int)$pdo->lastInsertId()], 201);
            break;

        // ================= UPDATE =================
        case 'PUT':
        case 'PATCH':
            // /users.php?id=3
            // Body (parcial): { "username":"otro", "password":"nuevo123", "role_id":1 }
            if (!$id) jsonResponse(['success' => false, 'message' => 'Falta id en la URL'], 400);

            // existe
            $chk = $pdo->prepare("SELECT id FROM users WHERE id = ?");
            $chk->execute([$id]);
            if (!$chk->fetch()) jsonResponse(['success' => false, 'message' => 'Usuario no encontrado'], 404);

            $data = getInputData();
            $username = array_key_exists('username', $data) ? sanitizeUsername($data['username']) : null;
            $password = $data['password'] ?? null; // null = no cambiar; "" = ignorar
            $role_id  = array_key_exists('role_id', $data) ? (int)$data['role_id'] : null;

            $fields = [];
            $params = [];

            if ($username !== null) {
                $dup = $pdo->prepare("SELECT id FROM users WHERE username = ? AND id <> ?");
                $dup->execute([$username, $id]);
                if ($dup->fetch()) jsonResponse(['success' => false, 'message' => 'El nombre de usuario ya está en uso'], 409);
                $fields[] = "username = ?";
                $params[] = $username;
            }

            if ($password !== null) {
                if ($password !== '') {
                    if (strlen($password) < 6) {
                        jsonResponse(['success' => false, 'message' => 'La contraseña debe tener al menos 6 caracteres'], 400);
                    }
                    $fields[] = "password = ?";
                    $params[] = password_hash($password, PASSWORD_BCRYPT);
                }
            }

            if ($role_id !== null) {
                $fields[] = "role_id = ?";
                $params[] = $role_id;
            }

            if (empty($fields)) {
                jsonResponse(['success' => false, 'message' => 'Nada que actualizar'], 400);
            }

            $sql = "UPDATE users SET " . implode(', ', $fields) . " WHERE id = ?";
            $params[] = $id;
            $upd = $pdo->prepare($sql);
            $upd->execute($params);

            jsonResponse(['success' => true, 'message' => 'Usuario actualizado']);
            break;

        // ================= DELETE =================
        case 'DELETE':
            // /users.php?id=3
            if (!$id) jsonResponse(['success' => false, 'message' => 'Falta id en la URL'], 400);
            if ($id === (int)$_SESSION['user_id']) {
                jsonResponse(['success' => false, 'message' => 'No puedes eliminar tu propia cuenta en sesión'], 400);
            }

            $del = $pdo->prepare("DELETE FROM users WHERE id = ?");
            $del->execute([$id]);

            if ($del->rowCount() === 0) {
                jsonResponse(['success' => false, 'message' => 'Usuario no encontrado'], 404);
            }
            jsonResponse(['success' => true, 'message' => 'Usuario eliminado']);
            break;

        default:
            jsonResponse(['success' => false, 'message' => 'Método no permitido'], 405);
    }
} catch (Throwable $e) {
    // error_log($e->getMessage());
    jsonResponse(['success' => false, 'message' => 'Error en la operación'], 500);
}
