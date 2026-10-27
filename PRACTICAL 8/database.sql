CREATE DATABASE IF NOT EXISTS StudentHub
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE StudentHub;

CREATE TABLE students (
    student_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    course VARCHAR(100) NOT NULL
);

CREATE TABLE events (
    event_id INT AUTO_INCREMENT PRIMARY KEY,
    event_name VARCHAR(150) NOT NULL,
    event_date DATE NOT NULL,
    venue VARCHAR(150) NOT NULL
);

CREATE TABLE registrations (
    registration_id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    event_id INT NOT NULL,
    registration_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_registration_student
        FOREIGN KEY (student_id)
        REFERENCES students(student_id)
        ON DELETE CASCADE,

    CONSTRAINT fk_registration_event
        FOREIGN KEY (event_id)
        REFERENCES events(event_id)
        ON DELETE CASCADE,

    CONSTRAINT unique_student_event
        UNIQUE (student_id, event_id)
);

-- Seed data: students
INSERT INTO students (name, email, course) VALUES
('Aarav Patel', 'aarav@example.com', 'B.Tech IT'),
('Diya Shah', 'diya@example.com', 'B.Tech CSE'),
('Krish Mehta', 'krish@example.com', 'BCA'),
('Anaya Desai', 'anaya@example.com', 'B.Tech IT'),
('Reyansh Joshi', 'reyansh@example.com', 'MCA');

-- Seed data: events
INSERT INTO events (event_name, event_date, venue) VALUES
('Web Development Workshop', '2026-11-10', 'Lab 1'),
('Coding Competition', '2026-11-15', 'Computer Lab'),
('Tech Fest', '2026-11-20', 'Main Auditorium'),
('Database Seminar', '2026-11-25', 'Seminar Hall');

-- Seed data: registrations
INSERT INTO registrations (student_id, event_id) VALUES
(1, 1),
(1, 2),
(2, 1),
(2, 3),
(3, 2),
(4, 4),
(5, 3);