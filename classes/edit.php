<?php
// classes/edit.php - Sửa lớp học
require_once '../config/config.php';
requireLogin();

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    header('Location: ' . SITE_URL . '/classes/index.php');
    exit();
}

$stmt = $conn->prepare("SELECT * FROM classes WHERE id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$class = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$class) {
    $_SESSION['flash_msg']  = 'Không tìm thấy lớp học.';
    $_SESSION['flash_type'] = 'error';
    header('Location: ' . SITE_URL . '/classes/index.php');
    exit();
}

$pageTitle = 'Sửa lớp: ' . $class['class_name'];
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $class_code  = trim($_POST['class_code']  ?? '');
    $class_name  = trim($_POST['class_name']  ?? '');
    $teacher     = trim($_POST['teacher']     ?? '');
    $description = trim($_POST['description'] ?? '');

    if (empty($class_code)) $errors[] = 'Mã lớp không được để trống.';
    if (empty($class_name)) $errors[] = 'Tên lớp không được để trống.';

    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT id FROM classes WHERE class_code = ? AND id != ?");
        $stmt->bind_param('si', $class_code, $id);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) $errors[] = 'Mã lớp đã tồn tại.';
        $stmt->close();
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("UPDATE classes SET class_code=?, class_name=?, teacher=?, description=? WHERE id=?");
        $stmt->bind_param('ssssi', $class_code, $class_name, $teacher, $description, $id);

        if ($stmt->execute()) {
            $_SESSION['flash_msg']  = 'Cập nhật lớp học thành công!';
            $_SESSION['flash_type'] = 'success';
            header('Location: ' . SITE_URL . '/classes/index.php');
            exit();
        } else {
            $errors[] = 'Đã có lỗi xảy ra. Vui lòng thử lại.';
        }
        $stmt->close();
    }

    $class = array_merge($class, $_POST);
}

include '../includes/header.php';
include '../includes/navbar.php';
?>

<div class="main-content">
    <div class="page-header">
        <div>
            <h1>✏️ Sửa lớp học</h1>
            <span class="breadcrumb">
                <a href="<?php echo SITE_URL; ?>/dashboard.php">Dashboard</a> &rsaquo;
                <a href="<?php echo SITE_URL; ?>/classes/index.php">Lớp học</a> &rsaquo;
                Chỉnh sửa
            </span>
        </div>
    </div>

    <?php if ($errors): ?>
        <div class="alert alert-error">
            <?php foreach ($errors as $e) echo '<div>' . htmlspecialchars($e, ENT_QUOTES, 'UTF-8') . '</div>'; ?>
        </div>
    <?php endif; ?>

    <div class="form-container">
        <form id="classForm" method="POST" action="edit.php?id=<?php echo $id; ?>">
            <div class="form-row">
                <div class="form-group">
                    <label for="class_code">Mã lớp <span class="required">*</span></label>
                    <input type="text" id="class_code" name="class_code" class="form-control"
                        value="<?php echo htmlspecialchars($class['class_code'], ENT_QUOTES, 'UTF-8'); ?>">
                </div>
                <div class="form-group">
                    <label for="teacher">Giáo viên phụ trách</label>
                    <input type="text" id="teacher" name="teacher" class="form-control"
                        value="<?php echo htmlspecialchars($class['teacher'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                </div>
            </div>

            <div class="form-group">
                <label for="class_name">Tên lớp <span class="required">*</span></label>
                <input type="text" id="class_name" name="class_name" class="form-control"
                    value="<?php echo htmlspecialchars($class['class_name'], ENT_QUOTES, 'UTF-8'); ?>">
            </div>

            <div class="form-group">
                <label for="description">Mô tả</label>
                <textarea id="description" name="description" class="form-control" rows="4"><?php echo htmlspecialchars($class['description'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
            </div>

            <div class="form-actions">
                <a href="index.php" class="btn btn-secondary">Huỷ</a>
                <button type="submit" class="btn btn-warning">💾 Cập nhật</button>
            </div>
        </form>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
