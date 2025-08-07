<h2>Users</h2>
<form method="post" action="/users">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token); ?>">
    <input type="text" name="username" placeholder="Username">
    <input type="password" name="password" placeholder="Password">
    <select name="role">
        <option value="receptionist">Receptionist</option>
        <option value="admin">Admin</option>
    </select>
    <button type="submit">Create</button>
</form>
<table>
    <tr><th>Username</th><th>Role</th><th>Actions</th></tr>
    <?php foreach ($users as $u): ?>
    <tr>
        <form method="post" action="/users/<?= $u['id']; ?>/update">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token); ?>">
            <td><input type="text" name="username" value="<?= htmlspecialchars($u['username']); ?>"></td>
            <td>
                <select name="role">
                    <option value="receptionist" <?= $u['role']==='receptionist'?'selected':''; ?>>Receptionist</option>
                    <option value="admin" <?= $u['role']==='admin'?'selected':''; ?>>Admin</option>
                </select>
            </td>
            <td>
                <input type="password" name="password" placeholder="New password">
                <button type="submit">Update</button>
        </form>
        <form method="post" action="/users/<?= $u['id']; ?>/delete" style="display:inline;">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token); ?>">
            <button type="submit" onclick="return confirm('Delete?')">Delete</button>
        </form>
            </td>
    </tr>
    <?php endforeach; ?>
</table>
