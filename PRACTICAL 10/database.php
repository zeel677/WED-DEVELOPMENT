
CREATE DATABASE IF NOT EXISTS SecureRegistration;
USE SecureRegistration;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'student') NOT NULL DEFAULT 'student',
    last_login DATETIME NULL
);

CREATE TABLE IF NOT EXISTS remember_tokens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    selector CHAR(24) NOT NULL UNIQUE,
    token_hash CHAR(64) NOT NULL,
    expires_at DATETIME NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id)
        ON DELETE CASCADE
);
