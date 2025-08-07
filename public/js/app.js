document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.status-select').forEach(sel => {
        sel.addEventListener('change', () => {
            const id = sel.dataset.id;
            const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            fetch(`/notes/${id}/status`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `status=${encodeURIComponent(sel.value)}&csrf_token=${encodeURIComponent(csrf)}`
            }).then(() => {
                sel.closest('.note').classList.remove('pending','completed');
                sel.closest('.note').classList.add(sel.value.toLowerCase());
            });
        });
    });
});
