<?php
// auth/check_role.php - Kiểm tra và phân quyền người dùng

/**
 * Yêu cầu người dùng phải có role cụ thể để truy cập trang.
 * - Nếu chưa đăng nhập → chuyển về trang login
 * - Nếu không đủ quyền → chuyển về dashboard với flash message lỗi
 *
 * @param string $requiredRole  'admin' hoặc 'teacher'
 */
function requireRole($requiredRole) {
    if (!isLoggedIn()) {
        header('Location: ' . SITE_URL . '/auth/login.php');
        exit();
    }
    if ($_SESSION['role'] !== $requiredRole) {
        $_SESSION['flash_msg']  = 'Bạn không có quyền truy cập trang này.';
        $_SESSION['flash_type'] = 'error';
        header('Location: ' . SITE_URL . '/dashboard.php');
        exit();
    }
}

/**
 * Trả về role hiện tại của người dùng đang đăng nhập.
 *
 * @return string|null
 */
function currentRole() {
    return $_SESSION['role'] ?? null;
}

/**
 * Kiểm tra người dùng có role cho trước hay không (không redirect).
 *
 * @param string $role
 * @return bool
 */
function hasRole($role) {
    return isLoggedIn() && $_SESSION['role'] === $role;
}
