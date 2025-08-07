<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\User;

class UserController extends Controller
{
    private function ensureAdmin(): void
    {
        // Guard to ensure only admins access user management
        if (!Auth::authorize('admin')) { http_response_code(403); exit('Forbidden'); }
    }

    public function index(): void
    {
        $this->ensureAdmin();
        $users = User::all();
        $this->view('users/index', ['users' => $users, 'csrf_token' => $this->csrf()]);
    }

    public function store(): void
    {
        $this->ensureAdmin();
        if (!$this->verifyCsrf()) { die('CSRF token mismatch'); }
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $role = $_POST['role'] ?? 'receptionist';
        if ($username && $password) {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            User::create($username, $hash, $role);
        }
        $this->redirect('/users');
    }

    public function update($id): void
    {
        $this->ensureAdmin();
        if (!$this->verifyCsrf()) { die('CSRF token mismatch'); }
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $role = $_POST['role'] ?? 'receptionist';
        $hash = $password ? password_hash($password, PASSWORD_DEFAULT) : null;
        if ($username) {
            User::update((int)$id, $username, $role, $hash);
        }
        $this->redirect('/users');
    }

    public function delete($id): void
    {
        $this->ensureAdmin();
        if (!$this->verifyCsrf()) { die('CSRF token mismatch'); }
        User::delete((int)$id);
        $this->redirect('/users');
    }
}
