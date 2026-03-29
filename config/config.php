<?php
// =============================================
// config/config.php - Cấu hình kết nối database
// =============================================

// Bắt đầu session ngay đầu file
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'student_management');
define('DB_CHARSET', 'utf8mb4');

// Kết nối database
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die('<div style="color:red;padding:20px;">
        <strong>Lỗi kết nối database:</strong> ' . $conn->connect_error . '
        <br>Vui lòng kiểm tra cấu hình trong file config/config.php
    </div>');
}

// Thiết lập charset UTF-8
$conn->set_charset(DB_CHARSET);

// Hằng số chung của ứng dụng
define('SITE_NAME', 'Student Management');
define('SITE_URL', 'http://localhost/student-management-web');

// Autoload helper functions
require_once __DIR__ . '/../helpers/functions.php';

// Hàm kiểm tra đăng nhập
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Hàm chuyển hướng nếu chưa đăng nhập
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: ' . SITE_URL . '/auth/login.php');
        exit();
    }
}

// Autoload role helpers (phụ thuộc vào isLoggedIn)
require_once __DIR__ . '/../auth/check_role.php';
