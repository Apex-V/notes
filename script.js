const loginSection = document.getElementById('loginSection');
const registerSection = document.getElementById('registerSection');
const noteSection = document.getElementById('noteSection');
const notesList = document.getElementById('notesList');
const noteInput = document.getElementById('noteInput');
const userDisplay = document.getElementById('userDisplay');

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
      if (loggedUser) {
          userDisplay.textContent = loggedUser;
          loginSection.style.display = 'none';
          noteSection.style.display = 'block';
          renderNotes();
      }
  };

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
          loginSection.style.display = 'none';
          noteSection.style.display = 'block';
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
        noteDiv.className = 'note ' + (note.status === 'Completed' ? 'completed' : 'pending');

        const updatedInfo = note.updated_by ? `<div class="note-meta">🔄 ${note.updated_by} | 🕒 ${note.updated_at}</div>` : '';

        noteDiv.innerHTML = `
      <div class="note-meta">👤 ${note.username} | 🕒 ${note.created_at}</div>
      <div>${note.content}</div>
      <div class="note-meta">Estado: ${note.status}</div>
      <select onchange="changeStatus(${note.id}, this.value)">
        <option value="Pending" ${note.status === 'Pending' ? 'selected' : ''}>Pending</option>
        <option value="Completed" ${note.status === 'Completed' ? 'selected' : ''}>Completed</option>
      </select>
      ${updatedInfo}
    `;

        notesList.appendChild(noteDiv);
    });
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

