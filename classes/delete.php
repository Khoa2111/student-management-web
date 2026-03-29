<?php
// classes/delete.php - Xoá lớp học
require_once '../config/config.php';
require_once '../auth/check_role.php';
requireRole('admin');

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    header('Location: ' . SITE_URL . '/classes/index.php');
    exit();
}

// Kiểm tra có sinh viên trong lớp không
$stmt = $conn->prepare("SELECT COUNT(*) FROM students WHERE class_id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$count = $stmt->get_result()->fetch_row()[0];
$stmt->close();

if ($count > 0) {
    $_SESSION['flash_msg']  = "Không thể xoá lớp này vì vẫn còn $count sinh viên trong lớp. Hãy chuyển sinh viên sang lớp khác trước.";
    $_SESSION['flash_type'] = 'error';
    header('Location: ' . SITE_URL . '/classes/index.php');
    exit();
}

$stmt = $conn->prepare("DELETE FROM classes WHERE id = ?");
$stmt->bind_param('i', $id);

if ($stmt->execute() && $stmt->affected_rows > 0) {
    $_SESSION['flash_msg']  = 'Đã xoá lớp học thành công.';
    $_SESSION['flash_type'] = 'success';
} else {
    $_SESSION['flash_msg']  = 'Không tìm thấy lớp học hoặc đã có lỗi xảy ra.';
    $_SESSION['flash_type'] = 'error';
}
$stmt->close();

header('Location: ' . SITE_URL . '/classes/index.php');
exit();
