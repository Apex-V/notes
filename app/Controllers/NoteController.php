<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\Note;

class NoteController extends Controller
{
    public function index(): void
    {
        if (!Auth::check()) { $this->redirect('/login'); }
        $notes = Note::all();
        $this->view('notes/index', [
            'notes' => $notes,
            'user' => Auth::user(),
            'csrf_token' => $this->csrf(),
        ]);
    }

    public function store(): void
    {
        if (!Auth::check()) { $this->redirect('/login'); }
        if (!$this->verifyCsrf()) { die('CSRF token mismatch'); }
        $content = trim($_POST['content'] ?? '');
        if ($content !== '') {
            Note::create(Auth::user()['id'], $content);
        }
        $this->redirect('/notes');
    }

    public function update($id): void
    {
        if (!Auth::check()) { $this->redirect('/login'); }
        if (!$this->verifyCsrf()) { die('CSRF token mismatch'); }
        $content = trim($_POST['content'] ?? '');
        if ($content !== '') {
            Note::update((int)$id, $content, Auth::user()['id']);
        }
        $this->redirect('/notes');
    }

    public function delete($id): void
    {
        // Admin only deletion
        if (!Auth::authorize('admin')) { http_response_code(403); exit('Forbidden'); }
        if (!$this->verifyCsrf()) { die('CSRF token mismatch'); }
        Note::delete((int)$id);
        $this->redirect('/notes');
    }

    public function status($id): void
    {
        // Admin only status change
        if (!Auth::authorize('admin')) { http_response_code(403); exit('Forbidden'); }
        if (!$this->verifyCsrf()) { die('CSRF token mismatch'); }
        $status = $_POST['status'] ?? 'Pending';
        Note::updateStatus((int)$id, $status, Auth::user()['id']);
        echo 'ok';
    }
}
