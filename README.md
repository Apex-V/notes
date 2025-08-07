# Notes MVC Application

Simple notes application built with PHP 8, PDO and MySQL using a basic MVC pattern with role based access control.

## Setup

1. Create MySQL database and run `database/schema.sql`. Optionally run `database/seed.sql` to create default admin (`admin` / `admin123`).
2. Copy `config/config.php` and adjust database credentials or use environment variables `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS`.
3. Serve the `public` directory with Apache (using the provided `.htaccess`) or PHP built-in server:
   ```
   php -S localhost:8000 -t public
   ```
4. Login with admin account and create additional users. Receptionists can manage notes but cannot change status.

## Routes
- GET /login, POST /login, POST /logout
- GET /notes, POST /notes, POST /notes/{id}/update, POST /notes/{id}/delete (admin), POST /notes/{id}/status (admin)
- GET /users (admin), POST /users (admin), POST /users/{id}/update (admin), POST /users/{id}/delete (admin)
