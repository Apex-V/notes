<?php
use App\Core\Auth;
use App\Core\Csrf;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="csrf-token" content="<?= Csrf::token(); ?>">
    <meta charset="UTF-8">
    <title>Notes App</title>
    <link rel="stylesheet" href="/css/styles.css">
    <script src="/js/app.js" defer></script>
</head>
<body>
<nav>
<?php if (Auth::check()): ?>
    <a href="/notes">Notes</a>
    <?php if (Auth::authorize('admin')): ?>
        <a href="/users">Users</a>
    <?php endif; ?>
    <form action="/logout" method="post" style="display:inline;">
        <input type="hidden" name="csrf_token" value="<?= Csrf::token(); ?>">
        <button type="submit">Logout (<?= htmlspecialchars(Auth::user()['username']); ?>)</button>
    </form>
<?php endif; ?>
</nav>
<div class="container">
