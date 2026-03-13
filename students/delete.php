<?php
// students/delete.php - Xoá sinh viên
// Lưu ý: Bảng enrollments có ON DELETE CASCADE nên sẽ tự động xoá
// các bản ghi liên quan khi xoá sinh viên.
require_once '../config/config.php';
requireLogin();

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    header('Location: ' . SITE_URL . '/students/index.php');
    exit();
}

$stmt = $conn->prepare("DELETE FROM students WHERE id = ?");
$stmt->bind_param('i', $id);

if ($stmt->execute() && $stmt->affected_rows > 0) {
    $_SESSION['flash_msg']  = 'Đã xoá sinh viên thành công.';
    $_SESSION['flash_type'] = 'success';
} else {
    $_SESSION['flash_msg']  = 'Không tìm thấy sinh viên hoặc đã có lỗi xảy ra.';
    $_SESSION['flash_type'] = 'error';
}
$stmt->close();

header('Location: ' . SITE_URL . '/students/index.php');
exit();
