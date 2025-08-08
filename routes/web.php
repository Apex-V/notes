<?php
use App\Controllers\AuthController;
use App\Controllers\NoteController;
use App\Controllers\UserController;

$auth = new AuthController();
$notes = new NoteController();
$users = new UserController();

$router->add('GET', '/login', [$auth, 'loginForm']);
$router->add('POST', '/login', [$auth, 'login']);
$router->add('POST', '/logout', [$auth, 'logout']);

$router->add('GET', '/notes', [$notes, 'index']);
$router->add('POST', '/notes', [$notes, 'store']);
$router->add('POST', '/notes/{id}/update', [$notes, 'update']);
$router->add('POST', '/notes/{id}/delete', [$notes, 'delete']);
$router->add('POST', '/notes/{id}/status', [$notes, 'status']);

$router->add('GET', '/users', [$users, 'index']);
$router->add('POST', '/users', [$users, 'store']);
$router->add('POST', '/users/{id}/update', [$users, 'update']);
$router->add('POST', '/users/{id}/delete', [$users, 'delete']);

$router->add('GET', '/', function() {
    header('Location: /notes');
});
