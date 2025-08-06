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

// Verifica si ya hay una sesión iniciada
window.onload = () => {
    const user = localStorage.getItem('loggedUser');
    if (user) {
        userDisplay.textContent = user;
        loginSection.style.display = 'none';
        noteSection.style.display = 'block';
    }
    renderNotes();
};

// Registrar usuario
function register() {
    const username = document.getElementById('registerUsername').value.trim();
    const password = document.getElementById('registerPassword').value;

    if (!username || !password) {
        alert('⚠️ Completa todos los campos');
        return;
    }

    const users = JSON.parse(localStorage.getItem('users')) || {};
    if (users[username]) {
        alert('⚠️ Usuario ya existe');
        return;
    }

    users[username] = password;
    localStorage.setItem('users', JSON.stringify(users));
    alert('✅ Registrado con éxito. Ahora inicia sesión');
    showLogin();
}

// Login
function login() {
    const username = document.getElementById('loginUsername').value.trim();
    const password = document.getElementById('loginPassword').value;

    const users = JSON.parse(localStorage.getItem('users')) || {};
    if (users[username] && users[username] === password) {
        localStorage.setItem('loggedUser', username);
        userDisplay.textContent = username;
        loginSection.style.display = 'none';
        noteSection.style.display = 'block';
    } else {
        alert('❌ Usuario o contraseña incorrectos');
    }
}

// Logout
function logout() {
    localStorage.removeItem('loggedUser');
    location.reload();
}

// Agregar nota
function addNote() {
    const content = noteInput.value.trim();
    const user = localStorage.getItem('loggedUser');

    if (!content) {
        alert('⚠️ Escribe algo para publicar');
        return;
    }

    const newNote = {
        contenido: content,
        usuario: user,
        fecha: new Date().toLocaleString()
    };

    const notes = JSON.parse(localStorage.getItem('notes')) || [];
    notes.push(newNote);
    localStorage.setItem('notes', JSON.stringify(notes));

    noteInput.value = '';
    renderNotes();
}

// Mostrar todas las notas
function renderNotes() {
    const notes = JSON.parse(localStorage.getItem('notes')) || [];
    notesList.innerHTML = '';

    notes.reverse().forEach(note => {
        const noteDiv = document.createElement('div');
        noteDiv.className = 'note';

        noteDiv.innerHTML = `
      <div class="note-meta">👤 ${note.usuario} | 🕒 ${note.fecha}</div>
      <div>${note.contenido}</div>
    `;

        notesList.appendChild(noteDiv);
    });
}
