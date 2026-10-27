
CREATE DATABASE IF NOT EXISTS StudentManagement;
USE StudentManagement;

CREATE TABLE IF NOT EXISTS students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(120) NOT NULL UNIQUE,
    course VARCHAR(80) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    status ENUM('active', 'deleted') NOT NULL DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT IGNORE INTO students (name, email, course, phone) VALUES
('Aarav Patel', 'aarav@example.com', 'B.Tech IT', '9876500001'),
('Diya Shah', 'diya@example.com', 'B.Tech CSE', '9876500002'),
('Rohan Mehta', 'rohan@example.com', 'BCA', '9876500003'),
('Anaya Desai', 'anaya@example.com', 'B.Tech IT', '9876500004'),
('Vivaan Joshi', 'vivaan@example.com', 'B.Sc IT', '9876500005'),
('Isha Trivedi', 'isha@example.com', 'B.Tech CSE', '9876500006'),
('Arjun Patel', 'arjun@example.com', 'BCA', '9876500007'),
('Kiara Shah', 'kiara@example.com', 'B.Tech IT', '9876500008'),
('Reyansh Mehta', 'reyansh@example.com', 'B.Sc IT', '9876500009'),
('Saanvi Desai', 'saanvi@example.com', 'B.Tech CSE', '9876500010'),
('Aditya Joshi', 'aditya@example.com', 'BCA', '9876500011'),
('Myra Trivedi', 'myra@example.com', 'B.Tech IT', '9876500012'),
('Krish Patel', 'krish@example.com', 'B.Sc IT', '9876500013'),
('Pari Shah', 'pari@example.com', 'B.Tech CSE', '9876500014'),
('Dhruv Mehta', 'dhruv@example.com', 'BCA', '9876500015'),
('Aadhya Desai', 'aadhya@example.com', 'B.Tech IT', '9876500016'),
('Kabir Joshi', 'kabir@example.com', 'B.Sc IT', '9876500017'),
('Navya Trivedi', 'navya@example.com', 'B.Tech CSE', '9876500018'),
('Yash Patel', 'yash@example.com', 'BCA', '9876500019'),
('Mira Shah', 'mira@example.com', 'B.Tech IT', '9876500020');
