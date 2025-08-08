<?php
use App\Core\Auth;
use App\Core\Csrf;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="csrf-token" content="<?= Csrf::token(); ?>">
    <meta name="base-path" content="<?= BASE_PATH; ?>">
    <meta charset="UTF-8">
    <title>Notes App</title>
    <link rel="stylesheet" href="<?= BASE_PATH; ?>/css/styles.css">
    <script src="<?= BASE_PATH; ?>/js/app.js" defer></script>
</head>
<body>
<nav>
<?php if (Auth::check()): ?>
    <a href="<?= BASE_PATH; ?>/notes">Notes</a>
    <?php if (Auth::authorize('admin')): ?>
        <a href="<?= BASE_PATH; ?>/users">Users</a>
    <?php endif; ?>
    <form action="<?= BASE_PATH; ?>/logout" method="post" style="display:inline;">
        <input type="hidden" name="csrf_token" value="<?= Csrf::token(); ?>">
        <button type="submit">Logout (<?= htmlspecialchars(Auth::user()['username']); ?>)</button>
    </form>
<?php endif; ?>
</nav>
<div class="container">
