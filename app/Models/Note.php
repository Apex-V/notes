<?php
namespace App\Models;

use App\Core\Db;

class Note
{
    public static function all(): array
    {
        $sql = 'SELECT n.*, u.username, ub.username AS updated_by_name FROM notes n JOIN users u ON n.user_id = u.id LEFT JOIN users ub ON n.updated_by = ub.id ORDER BY n.created_at DESC';
        return Db::getInstance()->query($sql)->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $stmt = Db::getInstance()->prepare('SELECT * FROM notes WHERE id=?');
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public static function create(int $userId, string $content): bool
    {
        $stmt = Db::getInstance()->prepare('INSERT INTO notes (user_id, content, status, created_at, updated_at) VALUES (?,?,?,?,?)');
        $now = date('Y-m-d H:i:s');
        return $stmt->execute([$userId, $content, 'Pending', $now, $now]);
    }

    public static function update(int $id, string $content, int $userId): bool
    {
        $now = date('Y-m-d H:i:s');
        $stmt = Db::getInstance()->prepare('UPDATE notes SET content=?, updated_at=?, updated_by=? WHERE id=?');
        return $stmt->execute([$content, $now, $userId, $id]);
    }

    public static function delete(int $id): bool
    {
        $stmt = Db::getInstance()->prepare('DELETE FROM notes WHERE id=?');
        return $stmt->execute([$id]);
    }

    public static function updateStatus(int $id, string $status, int $userId): bool
    {
        $now = date('Y-m-d H:i:s');
        $stmt = Db::getInstance()->prepare('UPDATE notes SET status=?, updated_at=?, updated_by=? WHERE id=?');
        return $stmt->execute([$status, $now, $userId, $id]);
    }
}
