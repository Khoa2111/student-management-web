-- ============================================================
-- database/migration_v2.sql
-- Migration: Add user_id to students, teacher_id to classes,
--            status to users
-- Compatible with MySQL 5.7+ / MariaDB 10.2+
-- Run once against the student_management database.
-- ============================================================

USE student_management;

-- 1. Add status column to users (active/inactive)
ALTER TABLE users
    ADD COLUMN IF NOT EXISTS `status` ENUM('active', 'inactive') NOT NULL DEFAULT 'active'
    AFTER role;

-- 2. Add teacher_id column to classes (FK → users.id)
ALTER TABLE classes
    ADD COLUMN IF NOT EXISTS teacher_id INT DEFAULT NULL
    AFTER teacher;

ALTER TABLE classes
    ADD CONSTRAINT fk_classes_teacher
    FOREIGN KEY (teacher_id) REFERENCES users(id)
    ON DELETE SET NULL ON UPDATE CASCADE;

-- 3. Add user_id column to students (FK → users.id)
ALTER TABLE students
    ADD COLUMN IF NOT EXISTS user_id INT DEFAULT NULL
    AFTER class_id;

ALTER TABLE students
    ADD CONSTRAINT fk_students_user
    FOREIGN KEY (user_id) REFERENCES users(id)
    ON DELETE SET NULL ON UPDATE CASCADE;

-- 4. Sample teacher accounts (password: teacher123)
INSERT IGNORE INTO users (username, email, password, full_name, role, status) VALUES
('teacher1', 'teacher1@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Nguyễn Văn A', 'teacher', 'active'),
('teacher2', 'teacher2@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Trần Thị B', 'teacher', 'active');
