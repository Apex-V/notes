<?php $note = $current; ?>
<div class="note <?= strtolower($note['status']); ?>">
    <p><strong><?= htmlspecialchars($note['username']); ?></strong> - <?= $note['created_at']; ?></p>
    <?php if ($note['updated_by_name']): ?>
    <p>Updated by <?= htmlspecialchars($note['updated_by_name']); ?> at <?= $note['updated_at']; ?></p>
    <?php endif; ?>
    <form method="post" action="/notes/<?= $note['id']; ?>/update">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token); ?>">
        <textarea name="content" rows="3" cols="40"><?= htmlspecialchars($note['content']); ?></textarea>
        <button type="submit">Update</button>
        <?php if ($user['role'] === 'admin'): ?>
            <button formaction="/notes/<?= $note['id']; ?>/delete" formmethod="post" onclick="return confirm('Delete?')">Delete</button>
        <?php endif; ?>
    </form>
    <div>
        Status:
        <?php if ($user['role'] === 'admin'): ?>
            <select class="status-select" data-id="<?= $note['id']; ?>" name="status">
                <option value="Pending" <?= $note['status'] === 'Pending' ? 'selected' : ''; ?>>Pending</option>
                <option value="Completed" <?= $note['status'] === 'Completed' ? 'selected' : ''; ?>>Completed</option>
            </select>
        <?php else: ?>
            <?= htmlspecialchars($note['status']); ?>
        <?php endif; ?>
    </div>
</div>
