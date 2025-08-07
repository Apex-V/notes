<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;

class AuthController extends Controller
{
    public function loginForm(): void
    {
        Auth::seedAdmin();
        $this->view('auth/login', ['csrf_token' => $this->csrf()]);
    }

    public function login(): void
    {
        if (!$this->verifyCsrf()) { die('CSRF token mismatch'); }
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
        if (Auth::attempt($username, $password)) {
            $this->redirect('/notes');
        } else {
            $this->view('auth/login', ['error' => 'Invalid credentials', 'csrf_token' => $this->csrf()]);
        }
    }

    public function logout(): void
    {
        Auth::logout();
        $this->redirect('/login');
    }
}
