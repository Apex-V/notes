const loginSection = document.getElementById('loginSection');
const registerSection = document.getElementById('registerSection');
const noteSection = document.getElementById('noteSection');
const notesList = document.getElementById('notesList');
const noteInput = document.getElementById('noteInput');
const addNoteBtn = document.getElementById('addNoteBtn');
const manageUsersLink = document.getElementById('manageUsersLink');
const userDisplay = document.getElementById('userDisplay');

let currentRole = typeof loggedRole !== 'undefined' ? loggedRole : null;

// Mostrar u ocultar secciones
function showRegister() {
    loginSection.style.display = 'none';
    registerSection.style.display = 'block';
}

function showLogin() {
    registerSection.style.display = 'none';
    loginSection.style.display = 'block';
}

// Verifica si hay una sesión iniciada
window.onload = () => {
    if (typeof loggedUser !== 'undefined' && loggedUser &&
        loginSection && noteSection && userDisplay) {
        userDisplay.textContent = loggedUser;
        loginSection.style.display = 'none';
        noteSection.style.display = 'block';
        applyRolePermissions();
        renderNotes();
    }
};

function applyRolePermissions() {
    if (currentRole === 1) {
        if (noteInput) noteInput.style.display = 'block';
        if (addNoteBtn) addNoteBtn.style.display = 'inline-block';
        if (manageUsersLink) manageUsersLink.style.display = 'inline';
    } else {
        if (noteInput) noteInput.style.display = 'none';
        if (addNoteBtn) addNoteBtn.style.display = 'none';
        if (manageUsersLink) manageUsersLink.style.display = 'none';
    }
}

// Registrar usuario
async function register() {
    const username = document.getElementById('registerUsername').value.trim();
    const password = document.getElementById('registerPassword').value;

    if (!username || !password) {
        alert('⚠️ Completa todos los campos');
        return;
    }

    const fd = new FormData();
    fd.append('username', username);
    fd.append('password', password);

    const res = await fetch('register.php', { method: 'POST', body: fd });
    const data = await res.json();
    if (data.success) {
        alert('✅ Registrado con éxito. Ahora inicia sesión');
        showLogin();
    } else {
        alert('⚠️ ' + data.message);
    }
}

// Login
async function login() {
    const username = document.getElementById('loginUsername').value.trim();
    const password = document.getElementById('loginPassword').value;

    const fd = new FormData();
    fd.append('username', username);
    fd.append('password', password);

    const res = await fetch('login.php', { method: 'POST', body: fd });
    const data = await res.json();
    if (data.success) {
        userDisplay.textContent = data.username;
        currentRole = data.role;
        loginSection.style.display = 'none';
        noteSection.style.display = 'block';
        applyRolePermissions();
        renderNotes();
    } else {
        alert('❌ ' + data.message);
    }
}

// Logout
async function logout() {
    await fetch('logout.php');
    location.reload();
}

// Agregar nota
async function addNote() {
    const content = noteInput.value.trim();

    if (!content) {
        alert('⚠️ Escribe algo para publicar');
        return;
    }

    const fd = new FormData();
    fd.append('content', content);

    const res = await fetch('add_note.php', { method: 'POST', body: fd });
    const data = await res.json();
    if (data.success) {
        noteInput.value = '';
        renderNotes();
    } else {
        alert('⚠️ ' + data.message);
    }
}

// Mostrar todas las notas
async function renderNotes() {
    const res = await fetch('fetch_notes.php');
    const notes = await res.json();
    notesList.innerHTML = '';

    notes.forEach(note => {
        const noteDiv = document.createElement('div');
        noteDiv.classList.add('note');

        // Normaliza el estatus a minúsculas para la clase CSS
        const statusClass = (note.status || '').toString().trim().toLowerCase() === 'completed'
            ? 'completed'
            : 'pending';
        noteDiv.classList.add(statusClass);

        const updatedInfo = note.updated_by
            ? `<div class="note-meta">🔄 ${note.updated_by} | 🕒 ${note.updated_at}</div>`
            : '';

        const statusSelect = currentRole === 1
            ? `<select data-note-id="${note.id}">
                <option value="Pending" ${note.status === 'Pending' ? 'selected' : ''}>Pending</option>
                <option value="Completed" ${note.status === 'Completed' ? 'selected' : ''}>Completed</option>
              </select>`
            : '';

        // Genera el HTML
        noteDiv.innerHTML = `
      <div class="note-meta">👤 ${note.username} | 🕒 ${note.created_at}</div>
      <div>${note.content}</div>
      <div class="note-meta">Estado: <strong>${note.status}</strong></div>
      ${statusSelect}
      ${updatedInfo}
    `;

        if (currentRole === 1) {
            const selectEl = noteDiv.querySelector('select');
            selectEl.addEventListener('change', async (e) => {
                const newStatus = e.target.value;
                await changeStatus(note.id, newStatus);

                noteDiv.classList.remove('pending', 'completed');
                const newClass = newStatus.toLowerCase() === 'completed' ? 'completed' : 'pending';
                noteDiv.classList.add(newClass);

                const metaEstado = noteDiv.querySelector('.note-meta:nth-of-type(2)');
                if (metaEstado) metaEstado.innerHTML = `Estado: <strong>${newStatus}</strong>`;
            });
        }

        notesList.appendChild(noteDiv);
    });
}

/* Ejemplo de changeStatus: ajusta a tu endpoint */
async function changeStatus(id, status) {
    // Devuelve promesa para poder await en el listener
    const res = await fetch('update_status.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({ id, status })
    });
    if (!res.ok) {
        console.error('No se pudo actualizar el estatus');
    }
}

async function changeStatus(id, status) {
    const fd = new FormData();
    fd.append('id', id);
    fd.append('status', status);
    const res = await fetch('update_status.php', { method: 'POST', body: fd });
    const data = await res.json();
    if (data.success) {
        renderNotes();
    } else {
        alert('⚠️ ' + data.message);
    }
}

// ======= USERS PANEL (append to script.js) =======
(() => {
    const app = document.getElementById('usersApp');
    if (!app) return; // No es la página de admin

    const tbody = app.querySelector('#usersTbody');
    const msg = app.querySelector('#usersMsg');
    const form = app.querySelector('#userForm');
    const formTitle = app.querySelector('#formTitle');
    const inputId = app.querySelector('#userId');
    const inputUsername = app.querySelector('#username');
    const inputPassword = app.querySelector('#password');
    const selectRole = app.querySelector('#role');
    const submitBtn = app.querySelector('#submitBtn');
    const resetBtn = app.querySelector('#resetBtn');
    const refreshBtn = app.querySelector('#refreshBtn');
    const searchUser = app.querySelector('#searchUser');
    const prevPage = app.querySelector('#prevPage');
    const nextPage = app.querySelector('#nextPage');
    const pageInfo = app.querySelector('#pageInfo');

    let currentPage = 1;
    let total = 0;
    let limit = 10;
    let rowsCache = [];

    function showMsg(text, type = 'ok') {
        msg.textContent = text;
        msg.className = `users-msg ${type}`;
        msg.hidden = false;
        setTimeout(() => (msg.hidden = true), 3000);
    }

    function roleLabel(val) {
        return Number(val) === 1 ? 'Administrador' : 'Recepcionista';
    }

    function renderRows(rows) {
        if (!rows.length) {
            tbody.innerHTML = `<tr><td colspan="5" class="empty">Sin resultados</td></tr>`;
            return;
        }
        tbody.innerHTML = rows.map(u => `
      <tr data-id="${u.id}">
        <td>${u.id}</td>
        <td>${escapeHtml(u.username)}</td>
        <td><span class="users-badge ${u.role_id == 1 ? 'admin' : 'recep'}">${roleLabel(u.role_id)}</span></td>
        <td>${u.created_at ?? ''}</td>
        <td class="col-actions">
          <button class="users-btn small" data-action="edit">Editar</button>
          <button class="users-btn small danger" data-action="delete">Eliminar</button>
        </td>
      </tr>
    `).join('');
    }

    function escapeHtml(s) {
        return String(s ?? '')
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#39;');
    }

    async function loadUsers(page = 1) {
        try {
            const res = await fetch(`users.php?page=${page}&limit=${limit}`, { credentials: 'include' });
            const data = await res.json();
            if (!data.success) throw new Error(data.message || 'Error al cargar');
            currentPage = data.meta.page;
            total = data.meta.total;
            rowsCache = data.data;
            applyFilter(); // render con filtro
            pageInfo.textContent = `Página ${currentPage} / ${Math.max(1, Math.ceil(total / limit))}`;
            prevPage.disabled = currentPage <= 1;
            nextPage.disabled = currentPage >= Math.ceil(total / limit);
        } catch (e) {
            console.error(e);
            showMsg('No se pudo cargar la lista', 'err');
        }
    }

    function applyFilter() {
        const q = (searchUser.value || '').trim().toLowerCase();
        if (!q) return renderRows(rowsCache);
        const filtered = rowsCache.filter(r => String(r.username).toLowerCase().includes(q));
        renderRows(filtered);
    }

    function resetForm() {
        form.reset();
        inputId.value = '';
        selectRole.value = '2';
        formTitle.textContent = 'Crear usuario';
        submitBtn.textContent = 'Crear';
    }

    async function createUser(payload) {
        const res = await fetch('users.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            credentials: 'include',
            body: JSON.stringify(payload),
        });
        return res.json();
    }

    async function updateUser(id, payload) {
        const res = await fetch(`users.php?id=${id}`, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            credentials: 'include',
            body: JSON.stringify(payload),
        });
        return res.json();
    }

    async function deleteUser(id) {
        const res = await fetch(`users.php?id=${id}`, {
            method: 'DELETE',
            credentials: 'include',
        });
        return res.json();
    }

    // Eventos
    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        const id = inputId.value ? Number(inputId.value) : null;
        const username = inputUsername.value.trim();
        const password = inputPassword.value; // si está vacío, no se cambia
        const role_id = Number(selectRole.value);

        if (!username || username.length < 3) {
            showMsg('Usuario inválido', 'err'); return;
        }
        if (!id && (!password || password.length < 6)) {
            showMsg('Contraseña mínima de 6 caracteres', 'err'); return;
        }

        try {
            const payload = { username, role_id };
            if (password !== '') payload.password = password;

            const data = id ? await updateUser(id, payload) : await createUser(payload);
            if (!data.success) throw new Error(data.message || 'Error');

            showMsg(id ? 'Usuario actualizado' : 'Usuario creado', 'ok');
            resetForm();
            await loadUsers(currentPage);
        } catch (err) {
            showMsg(err.message || 'Error en la operación', 'err');
        }
    });

    resetBtn.addEventListener('click', resetForm);
    refreshBtn.addEventListener('click', () => loadUsers(currentPage));
    searchUser.addEventListener('input', applyFilter);

    tbody.addEventListener('click', async (e) => {
        const btn = e.target.closest('button[data-action]');
        if (!btn) return;
        const tr = e.target.closest('tr[data-id]');
        if (!tr) return;
        const id = Number(tr.dataset.id);

        if (btn.dataset.action === 'edit') {
            // Cargar detalle para asegurar datos actuales
            try {
                const res = await fetch(`users.php?id=${id}`, { credentials: 'include' });
                const data = await res.json();
                if (!data.success) throw new Error(data.message || 'No encontrado');
                const u = data.data;
                inputId.value = u.id;
                inputUsername.value = u.username;
                inputPassword.value = ''; // no mostrar hash
                selectRole.value = String(u.role_id);
                formTitle.textContent = `Editar usuario #${u.id}`;
                submitBtn.textContent = 'Actualizar';
                window.scrollTo({ top: 0, behavior: 'smooth' });
            } catch (err) {
                showMsg('No se pudo cargar el usuario', 'err');
            }
        }

        if (btn.dataset.action === 'delete') {
            if (!confirm('¿Eliminar este usuario? Esta acción no se puede deshacer.')) return;
            try {
                const data = await deleteUser(id);
                if (!data.success) throw new Error(data.message || 'Error al eliminar');
                showMsg('Usuario eliminado', 'ok');
                await loadUsers(currentPage);
            } catch (err) {
                showMsg(err.message || 'No se pudo eliminar', 'err');
            }
        }
    });

    prevPage.addEventListener('click', () => {
        if (currentPage > 1) loadUsers(currentPage - 1);
    });
    nextPage.addEventListener('click', () => {
        const maxPage = Math.max(1, Math.ceil(total / limit));
        if (currentPage < maxPage) loadUsers(currentPage + 1);
    });

    // Init
    resetForm();
    loadUsers(1);
})();
