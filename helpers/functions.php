<?php
// =============================================
// helpers/functions.php - Reusable Helper Functions
// Student Management System
// =============================================

/**
 * Làm sạch dữ liệu hiển thị ra HTML (dùng tại điểm output).
 */
function sanitize($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

/**
 * Định dạng ngày tháng.
 *
 * @param string $date   Chuỗi ngày (Y-m-d hoặc bất kỳ định dạng DateTime hợp lệ)
 * @param string $format Định dạng đầu ra (mặc định: d/m/Y)
 * @return string
 */
function formatDate($date, $format = 'd/m/Y') {
    if (empty($date)) {
        return '';
    }
    $dt = DateTime::createFromFormat('Y-m-d', $date);
    if (!$dt) {
        $dt = new DateTime($date);
    }
    return $dt ? $dt->format($format) : $date;
}

/**
 * Trả về tên CSS class của badge dựa theo điểm.
 *
 * @param float|string $grade
 * @return string  badge class (badge-success | badge-warning | badge-danger)
 */
function gradeClass($grade) {
    $grade = (float) $grade;
    if ($grade >= 8.0) {
        return 'badge-success';
    } elseif ($grade >= 5.0) {
        return 'badge-warning';
    }
    return 'badge-danger';
}

/**
 * Trả về HTML badge giới tính.
 *
 * @param string $gender  'Nam' | 'Nữ' | other
 * @return string
 */
function genderBadge($gender) {
    switch ($gender) {
        case 'Nam':
            return '<span class="badge badge-info">' . sanitize($gender) . '</span>';
        case 'Nữ':
            return '<span class="badge badge-success">' . sanitize($gender) . '</span>';
        default:
            return '<span class="badge badge-warning">' . sanitize($gender) . '</span>';
    }
}

/**
 * Đọc flash message từ session và xoá sau khi đọc.
 *
 * @return array|null  ['msg' => string, 'type' => string] hoặc null nếu không có
 */
function getFlash() {
    if (!empty($_SESSION['flash_msg'])) {
        $flash = [
            'msg'  => $_SESSION['flash_msg'],
            'type' => $_SESSION['flash_type'] ?? 'info',
        ];
        unset($_SESSION['flash_msg'], $_SESSION['flash_type']);
        return $flash;
    }
    return null;
}
