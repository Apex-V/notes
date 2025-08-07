<?php
namespace App\Core;

use App\Models\User;

class Auth
{
    public static function user(): ?array
    {
        return $_SESSION['user'] ?? null;
    }

    public static function check(): bool
    {
        return isset($_SESSION['user']);
    }

    public static function attempt(string $username, string $password): bool
    {
        $user = User::findByUsername($username);
        if ($user && password_verify($password, $user['password_hash'])) {
            session_regenerate_id(true);
            $_SESSION['user'] = $user;
            return true;
        }
        return false;
    }

    public static function logout(): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
        }
        session_destroy();
    }

    public static function authorize(string $role): bool
    {
        $user = self::user();
        return $user && $user['role'] === $role;
    }

    public static function seedAdmin(): void
    {
        if (User::count() === 0) {
            $password = password_hash('admin123', PASSWORD_DEFAULT);
            User::create('admin', $password, 'admin');
        }
    }
}
