CREATE DATABASE IF NOT EXISTS phen_university;
USE phen_university;

DROP TABLE IF EXISTS users;
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(120) NOT NULL,
    email VARCHAR(120) NOT NULL UNIQUE,
    role ENUM('alumni', 'student', 'professor', 'staff') NOT NULL DEFAULT 'alumni',
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

DROP TABLE IF EXISTS campus_people;
CREATE TABLE campus_people (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(120) NOT NULL,
    role ENUM('student', 'professor', 'staff', 'alumni') NOT NULL,
    department VARCHAR(100) NOT NULL,
    email VARCHAR(120) NOT NULL,
    phone VARCHAR(30) NOT NULL,
    profile_image VARCHAR(255) DEFAULT 'https://via.placeholder.com/120x120.png?text=Profile',
    bio TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO users (full_name, email, role, password_hash) VALUES
('Alex Carter', 'alex.carter@phenuni.com', 'alumni', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('Mia Johnson', 'mia.johnson@phenuni.com', 'student', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

INSERT INTO campus_people (full_name, role, department, email, phone, profile_image, bio) VALUES
('Liam Turner', 'student', 'Computer Science', 'liam.turner@phenuni.com', '+1-555-201-1100', 'https://via.placeholder.com/120x120.png?text=Profile', 'Final-year software engineering student and alumni volunteer.'),
('Sofia Reeves', 'student', 'Business Administration', 'sofia.reeves@phenuni.com', '+1-555-201-1101', 'https://via.placeholder.com/120x120.png?text=Profile', 'Student council member focused on entrepreneurship events.'),
('Dr. Hannah Blake', 'professor', 'Computer Science', 'hannah.blake@phenuni.com', '+1-555-302-2210', 'https://via.placeholder.com/120x120.png?text=Profile', 'Professor of distributed systems and AI collaboration research.'),
('Dr. Marcus Lee', 'professor', 'Economics', 'marcus.lee@phenuni.com', '+1-555-302-2211', 'https://via.placeholder.com/120x120.png?text=Profile', 'Teaches macroeconomics and international trade policy.'),
('Nora Kim', 'staff', 'Admissions Office', 'nora.kim@phenuni.com', '+1-555-410-3340', 'https://via.placeholder.com/120x120.png?text=Profile', 'Admissions specialist supporting transfer and alumni legacy applications.'),
('Ethan Cruz', 'staff', 'Career Services', 'ethan.cruz@phenuni.com', '+1-555-410-3341', 'https://via.placeholder.com/120x120.png?text=Profile', 'Coordinates internship and networking opportunities across partners.'),
('Avery Morgan', 'alumni', 'Engineering Alumni Board', 'avery.morgan@phenuni.com', '+1-555-520-4480', 'https://via.placeholder.com/120x120.png?text=Profile', 'Class of 2018, currently mentoring current students in tech careers.'),
('Isabella Shaw', 'alumni', 'Arts Alumni Board', 'isabella.shaw@phenuni.com', '+1-555-520-4481', 'https://via.placeholder.com/120x120.png?text=Profile', 'Class of 2016, helps organize annual alumni cultural events.');

