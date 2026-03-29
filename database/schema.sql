-- Student Management System Database Schema (v2 - Admin/Teacher RBAC)
-- Compatible with MySQL 5.7+ / MariaDB 10.2+
-- Run this script to DROP the old database and recreate with the new schema.

DROP DATABASE IF EXISTS student_management;
CREATE DATABASE student_management CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE student_management;

-- -----------------------------------------------------
-- Table: users (tài khoản đăng nhập)
-- role: chỉ còn 'admin' và 'teacher' — đã xóa 'student'
-- -----------------------------------------------------
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100),
    role ENUM('admin', 'teacher') NOT NULL DEFAULT 'teacher',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -----------------------------------------------------
-- Table: classes (lớp học)
-- teacher_id: FK liên kết với user có role 'teacher'
-- teacher: giữ lại để hiển thị nhanh (tên giáo viên)
-- -----------------------------------------------------
CREATE TABLE classes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    class_code VARCHAR(20) NOT NULL UNIQUE,
    class_name VARCHAR(100) NOT NULL,
    teacher_id INT DEFAULT NULL,
    teacher VARCHAR(100),
    description TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_classes_teacher FOREIGN KEY (teacher_id) REFERENCES users(id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -----------------------------------------------------
-- Table: students (sinh viên)
-- -----------------------------------------------------
CREATE TABLE students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_code VARCHAR(20) NOT NULL UNIQUE,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE,
    phone VARCHAR(15),
    gender ENUM('Nam', 'Nữ', 'Khác') DEFAULT 'Nam',
    birthday DATE,
    address TEXT,
    class_id INT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_students_class FOREIGN KEY (class_id) REFERENCES classes(id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -----------------------------------------------------
-- Table: subjects (môn học)
-- -----------------------------------------------------
CREATE TABLE subjects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    subject_code VARCHAR(20) NOT NULL UNIQUE,
    subject_name VARCHAR(100) NOT NULL,
    credits TINYINT UNSIGNED NOT NULL DEFAULT 3,
    description TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -----------------------------------------------------
-- Table: enrollments (đăng ký môn học / điểm số)
-- -----------------------------------------------------
CREATE TABLE enrollments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    subject_id INT NOT NULL,
    semester VARCHAR(20) NOT NULL,
    grade DECIMAL(4,2) DEFAULT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_enrollment (student_id, subject_id, semester),
    CONSTRAINT fk_enroll_student FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_enroll_subject FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================
-- Sample data
-- =====================================================

-- Admin account (password: admin123)
INSERT INTO users (username, email, password, full_name, role) VALUES
('admin', 'admin@example.com', '$2y$10$r5YyvYgsFnFVHi32XWGsdelMNGEvpDhgG4hcpYM6NFEpbPA2AGl0y', 'Administrator', 'admin');

-- Teacher accounts (password: teacher123)
INSERT INTO users (username, email, password, full_name, role) VALUES
('teacher1', 'teacher1@example.com', '$2y$10$DbsjYYIEVcB7Gs52g9HSG.uBC4NqgYQiAItBVnnoG2B1yG3JxiYEK', 'Nguyễn Văn A', 'teacher'),
('teacher2', 'teacher2@example.com', '$2y$10$DbsjYYIEVcB7Gs52g9HSG.uBC4NqgYQiAItBVnnoG2B1yG3JxiYEK', 'Trần Thị B', 'teacher'),
('teacher3', 'teacher3@example.com', '$2y$10$DbsjYYIEVcB7Gs52g9HSG.uBC4NqgYQiAItBVnnoG2B1yG3JxiYEK', 'Lê Văn C', 'teacher');

-- Classes (teacher1→CNTT01, teacher2→CNTT02, teacher3→KT01, QT01 chưa gán)
INSERT INTO classes (class_code, class_name, teacher_id, teacher, description) VALUES
('CNTT01', 'Công nghệ thông tin K1', 2, 'Nguyễn Văn A', 'Lớp CNTT khoá 1'),
('CNTT02', 'Công nghệ thông tin K2', 3, 'Trần Thị B',   'Lớp CNTT khoá 2'),
('KT01',   'Kế toán K1',            4, 'Lê Văn C',     'Lớp Kế toán khoá 1'),
('QT01',   'Quản trị kinh doanh K1',NULL, NULL, 'Lớp QTKD khoá 1');

-- Students
INSERT INTO students (student_code, full_name, email, phone, gender, birthday, address, class_id) VALUES
('SV001', 'Nguyễn Minh Tuấn', 'tuan.nm@example.com', '0901234567', 'Nam', '2003-05-10', 'Hà Nội',      1),
('SV002', 'Trần Thị Lan',     'lan.tt@example.com',  '0912345678', 'Nữ', '2003-08-22', 'Hồ Chí Minh', 1),
('SV003', 'Lê Văn Hùng',      'hung.lv@example.com', '0923456789', 'Nam', '2002-11-15', 'Đà Nẵng',    2),
('SV004', 'Phạm Thị Mai',     'mai.pt@example.com',  '0934567890', 'Nữ', '2003-03-18', 'Hải Phòng',   2),
('SV005', 'Hoàng Văn Nam',    'nam.hv@example.com',  '0945678901', 'Nam', '2002-07-25', 'Cần Thơ',     3),
('SV006', 'Đặng Thị Hoa',     'hoa.dt@example.com',  '0956789012', 'Nữ', '2003-01-09', 'Huế',         3),
('SV007', 'Bùi Văn Tùng',     'tung.bv@example.com', '0967890123', 'Nam', '2002-09-30', 'Nha Trang',   4),
('SV008', 'Ngô Thị Thu',      'thu.nt@example.com',  '0978901234', 'Nữ', '2003-12-05', 'Vũng Tàu',    4);

-- Subjects
INSERT INTO subjects (subject_code, subject_name, credits, description) VALUES
('LTUD',  'Lập trình ứng dụng',     3, 'Môn học lập trình ứng dụng cơ bản'),
('CSDL',  'Cơ sở dữ liệu',          3, 'Thiết kế và quản lý cơ sở dữ liệu'),
('MANG',  'Mạng máy tính',          3, 'Kiến trúc và giao thức mạng'),
('LTPTW', 'Lập trình web',          3, 'Phát triển web với HTML/CSS/PHP'),
('TOAN',  'Toán cao cấp',           4, 'Giải tích và đại số tuyến tính'),
('ANH',   'Tiếng Anh chuyên ngành', 3, 'Tiếng Anh cho sinh viên CNTT');

-- Enrollments (sample grades)
INSERT INTO enrollments (student_id, subject_id, semester, grade) VALUES
(1, 1, 'HK1-2023', 8.5),
(1, 2, 'HK1-2023', 7.0),
(1, 4, 'HK1-2023', 9.0),
(2, 1, 'HK1-2023', 7.5),
(2, 2, 'HK1-2023', 8.0),
(2, 5, 'HK1-2023', 6.5),
(3, 1, 'HK1-2023', 6.0),
(3, 3, 'HK1-2023', 7.5),
(4, 4, 'HK1-2023', 8.0),
(5, 5, 'HK1-2023', 9.5);
