<?php
namespace App\Models;

use App\Core\Db;
use PDO;

class User
{
    public static function findByUsername(string $username): ?array
    {
        $stmt = Db::getInstance()->prepare('SELECT * FROM users WHERE username = ?');
        $stmt->execute([$username]);
        return $stmt->fetch() ?: null;
    }

    public static function find(int $id): ?array
    {
        $stmt = Db::getInstance()->prepare('SELECT * FROM users WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public static function all(): array
    {
        return Db::getInstance()->query('SELECT * FROM users')->fetchAll();
    }

    public static function create(string $username, string $passwordHash, string $role): bool
    {
        $stmt = Db::getInstance()->prepare('INSERT INTO users (username, password_hash, role, created_at, updated_at) VALUES (?,?,?,?,?)');
        $now = date('Y-m-d H:i:s');
        return $stmt->execute([$username, $passwordHash, $role, $now, $now]);
    }

    public static function update(int $id, string $username, string $role, ?string $passwordHash = null): bool
    {
        $now = date('Y-m-d H:i:s');
        if ($passwordHash) {
            $stmt = Db::getInstance()->prepare('UPDATE users SET username=?, role=?, password_hash=?, updated_at=? WHERE id=?');
            return $stmt->execute([$username, $role, $passwordHash, $now, $id]);
        } else {
            $stmt = Db::getInstance()->prepare('UPDATE users SET username=?, role=?, updated_at=? WHERE id=?');
            return $stmt->execute([$username, $role, $now, $id]);
        }
    }

    public static function delete(int $id): bool
    {
        $stmt = Db::getInstance()->prepare('DELETE FROM users WHERE id=?');
        return $stmt->execute([$id]);
    }

    public static function count(): int
    {
        return (int) Db::getInstance()->query('SELECT COUNT(*) FROM users')->fetchColumn();
    }
}
