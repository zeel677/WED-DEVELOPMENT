
CREATE DATABASE IF NOT EXISTS StudentAPI;
USE StudentAPI;

CREATE TABLE IF NOT EXISTS students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(120) NOT NULL UNIQUE,
    course VARCHAR(80) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT IGNORE INTO students (name, email, course) VALUES
('Aarav Patel', 'aarav@example.com', 'B.Tech IT'),
('Diya Shah', 'diya@example.com', 'B.Tech CSE'),
('Rohan Mehta', 'rohan@example.com', 'BCA'),
('Anaya Desai', 'anaya@example.com', 'B.Tech IT'),
('Vivaan Joshi', 'vivaan@example.com', 'B.Sc IT'),
('Isha Trivedi', 'isha@example.com', 'B.Tech CSE'),
('Arjun Patel', 'arjun@example.com', 'BCA'),
('Kiara Shah', 'kiara@example.com', 'B.Tech IT'),
('Reyansh Mehta', 'reyansh@example.com', 'B.Sc IT'),
('Saanvi Desai', 'saanvi@example.com', 'B.Tech CSE'),
('Aditya Joshi', 'aditya@example.com', 'BCA'),
('Myra Trivedi', 'myra@example.com', 'B.Tech IT'),
('Krish Patel', 'krish@example.com', 'B.Sc IT'),
('Pari Shah', 'pari@example.com', 'B.Tech CSE'),
('Dhruv Mehta', 'dhruv@example.com', 'BCA'),
('Aadhya Desai', 'aadhya@example.com', 'B.Tech IT'),
('Kabir Joshi', 'kabir@example.com', 'B.Sc IT'),
('Navya Trivedi', 'navya@example.com', 'B.Tech CSE'),
('Yash Patel', 'yash@example.com', 'BCA'),
('Mira Shah', 'mira@example.com', 'B.Tech IT');
