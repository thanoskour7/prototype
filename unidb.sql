-- Δημιουργία βάσης δεδομένων
CREATE DATABASE IF NOT EXISTS university_database;
USE university_database;

-- Πίνακας ρόλων
CREATE TABLE IF NOT EXISTS roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL
);

INSERT INTO roles (name) VALUES ('student'), ('professor');

-- Πίνακας χρηστών
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (role_id) REFERENCES roles(id)
);

-- Πίνακας μαθημάτων
CREATE TABLE IF NOT EXISTS courses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    professor_id INT NOT NULL,
    is_visible TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (professor_id) REFERENCES users(id)
);

-- Πίνακας εγγραφών φοιτητών σε μαθήματα
CREATE TABLE IF NOT EXISTS enrollments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    course_id INT NOT NULL,
    enrolled_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES users(id),
    FOREIGN KEY (course_id) REFERENCES courses(id),
    UNIQUE KEY unique_enrollment (student_id, course_id)
);

-- Πίνακας βαθμολογιών
CREATE TABLE IF NOT EXISTS grades (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    course_id INT NOT NULL,
    grade DECIMAL(4,2),
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES users(id),
    FOREIGN KEY (course_id) REFERENCES courses(id)
);


-- Δημιουργία default καθηγητή για τα premade courses
-- Ο default καθηγητής έχει:
-- Username: admin_professor
-- Email: admin@university.gr
-- Password: password (πρέπει να αλλάξετε αυτό το password μετά την πρώτη σύνδεση!)
-- Role: professor (id = 2)
INSERT INTO users (username, email, password, role_id) VALUES
('admin_professor', 'admin@university.gr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 2);

-- Εισαγωγή premade courses (προ-δημιουργημένα μαθήματα)
-- Τα courses ανατίθενται στον default καθηγητή που μόλις δημιουργήθηκε.
-- Αν θέλετε να τα αναθέσετε σε άλλον καθηγητή, μπορείτε να τα επεξεργαστείτε
-- μέσω της σελίδας professor_courses.php.
-- 
-- ΣΗΜΑΝΤΙΚΟ: Το default password είναι 'password'.
-- Για ασφάλεια, συνιστάται να αλλάξετε το password του admin_professor
-- μετά την πρώτη σύνδεση. Μπορείτε να το κάνετε μέσω της σελίδας profile.php
-- (θα χρειαστεί να προσθέσετε λειτουργία αλλαγής password) ή να δημιουργήσετε
-- έναν νέο καθηγητή μέσω της register.php.

INSERT INTO courses (title, description, professor_id, is_visible) 
SELECT 
    'Εισαγωγή στον Προγραμματισμό',
    'Βασικές έννοιες προγραμματισμού με Python. Μεταβλητές, συναρτήσεις, loops και conditionals.',
    u.id,
    1
FROM users u 
WHERE u.username = 'admin_professor' AND u.role_id = 2
LIMIT 1;

INSERT INTO courses (title, description, professor_id, is_visible) 
SELECT 
    'Βάσεις Δεδομένων',
    'Σχεδίαση και υλοποίηση βάσεων δεδομένων. SQL queries, normalization, και database design.',
    u.id,
    1
FROM users u 
WHERE u.username = 'admin_professor' AND u.role_id = 2
LIMIT 1;

INSERT INTO courses (title, description, professor_id, is_visible) 
SELECT 
    'Web Development',
    'Ανάπτυξη web εφαρμογών με HTML, CSS, JavaScript και PHP.',
    u.id,
    1
FROM users u 
WHERE u.username = 'admin_professor' AND u.role_id = 2
LIMIT 1;

INSERT INTO courses (title, description, professor_id, is_visible) 
SELECT 
    'Αλγόριθμοι και Δομές Δεδομένων',
    'Σπουδαίοι αλγόριθμοι, sorting, searching, και data structures.',
    u.id,
    1
FROM users u 
WHERE u.username = 'admin_professor' AND u.role_id = 2
LIMIT 1;

INSERT INTO courses (title, description, professor_id, is_visible) 
SELECT 
    'Λειτουργικά Συστήματα',
    'Αρχιτεκτονική λειτουργικών συστημάτων, processes, threads, και memory management.',
    u.id,
    1
FROM users u 
WHERE u.username = 'admin_professor' AND u.role_id = 2
LIMIT 1;
