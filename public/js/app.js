document.addEventListener('DOMContentLoaded', () => {
    const base = document.querySelector('meta[name="base-path"]').getAttribute('content');
    document.querySelectorAll('.status-select').forEach(sel => {
        sel.addEventListener('change', () => {
            const id = sel.dataset.id;
            const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            fetch(`${base}/notes/${id}/status`, {
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
