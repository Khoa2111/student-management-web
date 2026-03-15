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

// =============================================
// Base URL – tự động theo môi trường chạy
// =============================================
$_smw_scheme     = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$_smw_host       = $_SERVER['HTTP_HOST'] ?? 'localhost';
$_smw_projectDir = realpath(__DIR__ . '/..');
$_smw_docRoot    = realpath($_SERVER['DOCUMENT_ROOT'] ?? '');

if ($_smw_docRoot !== false && $_smw_projectDir !== false) {
    $_smw_projectDir = str_replace('\\', '/', $_smw_projectDir);
    $_smw_docRoot    = str_replace('\\', '/', $_smw_docRoot);
    $_smw_urlPath    = (strpos($_smw_projectDir, $_smw_docRoot) === 0)
                        ? substr($_smw_projectDir, strlen($_smw_docRoot))
                        : '';
} else {
    $_smw_urlPath = '';
}

define('BASE_URL', $_smw_scheme . '://' . $_smw_host . rtrim($_smw_urlPath, '/'));
define('SITE_URL', BASE_URL); // alias giữ tương thích ngược

unset($_smw_scheme, $_smw_host, $_smw_projectDir, $_smw_docRoot, $_smw_urlPath);

// Autoload helper functions
require_once __DIR__ . '/../helpers/functions.php';

/**
 * Tạo URL tuyệt đối dựa trên BASE_URL.
 * Ví dụ: url('/css/style.css') -> http://localhost/my-project/css/style.css
 *
 * @param string $path  Đường dẫn bắt đầu bằng /
 * @return string
 */
function url($path = '') {
    $path = ltrim((string) $path, '/');
    return $path === '' ? BASE_URL : BASE_URL . '/' . $path;
}

// Hàm kiểm tra đăng nhập
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Hàm chuyển hướng nếu chưa đăng nhập
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: ' . url('/auth/login.php'));
        exit();
    }
}
