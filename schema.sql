-- Tabla de roles
CREATE TABLE roles (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(50) NOT NULL UNIQUE
);

-- Tabla de usuarios
CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role_id INT NOT NULL,
  FOREIGN KEY (role_id) REFERENCES roles(id)
);

-- Tabla de notas
CREATE TABLE notes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  content TEXT NOT NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id)
);

INSERT INTO roles (name) VALUES ('administrador'), ('recepcionista');

-- admin / admin123
-- recepcionista / recep456

INSERT INTO users (username, password, role_id) VALUES
('admin', '$2y$10$3RxdJGzLQpD5L1gFqFh4sODKgLx1ZzVHZPzzlxlm94dxcdPwlViZK', 1),
('recepcionista', '$2y$10$79pER4zLnh86Z1hhrcOe4e9DHDzOm3EsVoLZqZr6eQFeyyMNMYAiq', 2);
