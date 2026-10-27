CREATE DATABASE IF NOT EXISTS EventManagement;
USE EventManagement;

CREATE TABLE IF NOT EXISTS events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(150) NOT NULL,
    description TEXT NOT NULL,
    event_date DATE NOT NULL,
    category VARCHAR(80) NOT NULL,
    status ENUM('open', 'closed') NOT NULL DEFAULT 'open',
    poster VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
);

CREATE TABLE IF NOT EXISTS event_audit (
    id INT AUTO_INCREMENT PRIMARY KEY,
    admin_id INT NOT NULL,
    event_id INT NULL,
    action VARCHAR(20) NOT NULL,
    details VARCHAR(255) NOT NULL,
    changed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO events
(title, description, event_date, category, status)
VALUES
('Tech Fest', 'Technology exhibitions and coding activities.',
 '2027-01-15', 'Technology', 'open'),
('Cultural Night', 'Music, dance and cultural performances.',
 '2027-02-10', 'Cultural', 'open'),
('Sports Meet', 'Inter-department sports competitions.',
 '2027-02-20', 'Sports', 'open'),
('Web Development Workshop', 'Learn HTML, CSS and JavaScript.',
 '2027-03-05', 'Workshop', 'closed'),
('Entrepreneurship Seminar', 'Talks on startups and innovation.',
 '2027-03-18', 'Seminar', 'open');
