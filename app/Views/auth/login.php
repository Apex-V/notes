<h2>Login</h2>
<?php if (!empty($error)): ?>
<p style="color:red;"><?= htmlspecialchars($error); ?></p>
<?php endif; ?>
<form method="post" action="/login">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token); ?>">
    <div>
        <label>Username</label>
        <input type="text" name="username">
    </div>
    <div>
        <label>Password</label>
        <input type="password" name="password">
    </div>
    <button type="submit">Login</button>
</form>
