<h2>Notes</h2>
<form method="post" action="<?= BASE_PATH; ?>/notes">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token); ?>">
    <textarea name="content" rows="3" cols="40"></textarea>
    <button type="submit">Add Note</button>
</form>
<div class="notes-list">
<?php foreach ($notes as $note): ?>
    <?php $current = $note; include __DIR__ . '/item.php'; ?>
<?php endforeach; ?>
</div>
