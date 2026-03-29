<?php
// classes/add.php - Thêm lớp học mới
require_once '../config/config.php';
require_once '../auth/check_role.php';
requireRole('admin');

$pageTitle = 'Thêm lớp học';
$loadValidate = true;
$errors = [];

// Lấy danh sách giáo viên
$teacherList = $conn->query("SELECT id, full_name, username FROM users WHERE role = 'teacher' AND status = 'active' ORDER BY full_name");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $class_code  = trim($_POST['class_code']  ?? '');
    $class_name  = trim($_POST['class_name']  ?? '');
    $teacher_id  = intval($_POST['teacher_id'] ?? 0);
    $description = trim($_POST['description'] ?? '');

    if (empty($class_code)) $errors[] = 'Mã lớp không được để trống.';
    if (empty($class_name)) $errors[] = 'Tên lớp không được để trống.';

    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT id FROM classes WHERE class_code = ?");
        $stmt->bind_param('s', $class_code);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) $errors[] = 'Mã lớp đã tồn tại.';
        $stmt->close();
    }

    if (empty($errors)) {
        $teacherIdVal = $teacher_id > 0 ? $teacher_id : null;
        // Resolve teacher name from selected teacher account
        $teacherName = '';
        if ($teacherIdVal) {
            $stmtT = $conn->prepare("SELECT full_name, username FROM users WHERE id = ? AND role = 'teacher'");
            $stmtT->bind_param('i', $teacherIdVal);
            $stmtT->execute();
            $tRow = $stmtT->get_result()->fetch_assoc();
            $stmtT->close();
            $teacherName = $tRow ? ($tRow['full_name'] ?: $tRow['username']) : '';
        }
        $stmt = $conn->prepare("INSERT INTO classes (class_code, class_name, teacher, teacher_id, description) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param('sssis', $class_code, $class_name, $teacherName, $teacherIdVal, $description);

        if ($stmt->execute()) {
            $_SESSION['flash_msg']  = 'Thêm lớp học thành công!';
            $_SESSION['flash_type'] = 'success';
            header('Location: ' . SITE_URL . '/classes/index.php');
            exit();
        } else {
            $errors[] = 'Đã có lỗi xảy ra. Vui lòng thử lại.';
        }
        $stmt->close();
    }
}

include '../includes/header.php';
include '../includes/navbar.php';
?>

<div class="main-content">
    <div class="page-header">
        <div>
            <h1>➕ Thêm lớp học</h1>
            <span class="breadcrumb">
                <a href="<?php echo SITE_URL; ?>/dashboard.php">Dashboard</a> &rsaquo;
                <a href="<?php echo SITE_URL; ?>/classes/index.php">Lớp học</a> &rsaquo;
                Thêm mới
            </span>
        </div>
    </div>

    <?php if ($errors): ?>
        <div class="alert alert-error">
            <?php foreach ($errors as $e) echo '<div>' . htmlspecialchars($e, ENT_QUOTES, 'UTF-8') . '</div>'; ?>
        </div>
    <?php endif; ?>

    <div class="form-container">
        <form id="classForm" method="POST" action="add.php">
            <div class="form-row">
                <div class="form-group">
                    <label for="class_code">Mã lớp <span class="required">*</span></label>
                    <input type="text" id="class_code" name="class_code" class="form-control"
                        value="<?php echo htmlspecialchars($_POST['class_code'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                        placeholder="VD: CNTT01">
                </div>
                <div class="form-group">
                    <label for="teacher_id">Giáo viên phụ trách</label>
                    <select id="teacher_id" name="teacher_id" class="form-control">
                        <option value="0">-- Chọn giáo viên --</option>
                        <?php
                        $selectedTeacherId = intval($_POST['teacher_id'] ?? 0);
                        while ($t = $teacherList->fetch_assoc()):
                        ?>
                            <option value="<?php echo $t['id']; ?>"
                                <?php echo ($selectedTeacherId === $t['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($t['full_name'] ?: $t['username'], ENT_QUOTES, 'UTF-8'); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="class_name">Tên lớp <span class="required">*</span></label>
                <input type="text" id="class_name" name="class_name" class="form-control"
                    value="<?php echo htmlspecialchars($_POST['class_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                    placeholder="VD: Công nghệ thông tin K1">
            </div>

            <div class="form-group">
                <label for="description">Mô tả</label>
                <textarea id="description" name="description" class="form-control" rows="4"
                    placeholder="Mô tả về lớp học..."><?php echo htmlspecialchars($_POST['description'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
            </div>

            <div class="form-actions">
                <a href="index.php" class="btn btn-secondary">Huỷ</a>
                <button type="submit" class="btn btn-primary">💾 Lưu lớp học</button>
            </div>
        </form>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
