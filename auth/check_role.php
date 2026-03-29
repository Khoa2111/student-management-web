<?php
// auth/check_role.php - Role-based access control helpers
// Requires config/config.php (which defines isLoggedIn() and SITE_URL) to be loaded first.

/**
 * Require a specific role (or one of several roles) to access a page.
 * - Not logged in        → redirect to login.
 * - Logged in, wrong role → set flash error and redirect to dashboard.
 *
 * @param string|string[] $role  Required role(s): 'admin', 'teacher', 'student'
 */
function requireRole($role) {
    if (!isLoggedIn()) {
        header('Location: ' . SITE_URL . '/auth/login.php');
        exit();
    }

    $roles = is_array($role) ? $role : [$role];
    if (!in_array($_SESSION['role'] ?? '', $roles, true)) {
        $_SESSION['flash_msg']  = 'Bạn không có quyền truy cập trang này.';
        $_SESSION['flash_type'] = 'error';
        header('Location: ' . SITE_URL . '/dashboard.php');
        exit();
    }
}

/**
 * Return the current user's role from session.
 *
 * @return string
 */
function currentRole() {
    return $_SESSION['role'] ?? '';
}

/**
 * Check whether the current user has a specific role.
 *
 * @param string $role
 * @return bool
 */
function hasRole($role) {
    return ($_SESSION['role'] ?? '') === $role;
}
