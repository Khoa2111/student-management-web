<?php
// students/edit.php - Sửa thông tin sinh viên
require_once '../config/config.php';
require_once '../auth/check_role.php';
requireRole('admin');

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    header('Location: ' . SITE_URL . '/students/index.php');
    exit();
}

// Lấy thông tin sinh viên
$stmt->bind_param('i', $id);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$student) {
    $_SESSION['flash_msg']  = 'Không tìm thấy sinh viên.';
    $_SESSION['flash_type'] = 'error';
    header('Location: ' . SITE_URL . '/students/index.php');
    exit();
}

$pageTitle = 'Sửa sinh viên: ' . $student['full_name'];
$loadValidate = true;
$errors = [];

// Lấy danh sách lớp
$classList = $conn->query("SELECT id, class_name FROM classes ORDER BY class_name");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_code = trim($_POST['student_code'] ?? '');
    $full_name    = trim($_POST['full_name']    ?? '');
    $email        = trim($_POST['email']        ?? '');
    $phone        = trim($_POST['phone']        ?? '');
    $gender       = $_POST['gender']   ?? 'Nam';
    $birthday     = $_POST['birthday'] ?? '';
    $address      = trim($_POST['address']   ?? '');
    $class_id     = intval($_POST['class_id'] ?? 0);

    if (empty($student_code)) $errors[] = 'Mã sinh viên không được để trống.';
    if (empty($full_name))    $errors[] = 'Họ và tên không được để trống.';
    if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Email không hợp lệ.';
    if ($birthday && !DateTime::createFromFormat('Y-m-d', $birthday)) $errors[] = 'Ngày sinh không hợp lệ.';

    if (empty($errors)) {
        // Kiểm tra mã SV trùng với sinh viên khác
        $stmt = $conn->prepare("SELECT id FROM students WHERE student_code = ? AND id != ?");
        $stmt->bind_param('si', $student_code, $id);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) $errors[] = 'Mã sinh viên đã tồn tại.';
        $stmt->close();

        if ($email) {
            $stmt = $conn->prepare("SELECT id FROM students WHERE email = ? AND id != ?");
            $stmt->bind_param('si', $email, $id);
            $stmt->execute();
            $stmt->store_result();
            if ($stmt->num_rows > 0) $errors[] = 'Email đã được sử dụng bởi sinh viên khác.';
            $stmt->close();
        }
    }

    if (empty($errors)) {
        $birthdayVal = $birthday ?: null;
        $emailVal    = $email    ?: null;
        $classVal    = $class_id > 0 ? $class_id : null;

        $stmt = $conn->prepare(
            "UPDATE students SET student_code=?, full_name=?, email=?, phone=?, gender=?, birthday=?, address=?, class_id=?
             WHERE id=?"
        );
        $stmt->bind_param('sssssssii', $student_code, $full_name, $emailVal, $phone, $gender, $birthdayVal, $address, $classVal, $id);

        if ($stmt->execute()) {
            $_SESSION['flash_msg']  = 'Cập nhật sinh viên thành công!';
            $_SESSION['flash_type'] = 'success';
            header('Location: ' . SITE_URL . '/students/index.php');
            exit();
        } else {
            $errors[] = 'Đã có lỗi xảy ra. Vui lòng thử lại.';
        }
        $stmt->close();
    }

    // Cập nhật dữ liệu hiển thị trong form
    $student = array_merge($student, $_POST);
}

include '../includes/header.php';
include '../includes/navbar.php';
?>

<div class="main-content">
    <div class="page-header">
        <div>
            <h1>✏️ Sửa sinh viên</h1>
            <span class="breadcrumb">
                <a href="<?php echo SITE_URL; ?>/dashboard.php">Dashboard</a> &rsaquo;
                <a href="<?php echo SITE_URL; ?>/students/index.php">Sinh viên</a> &rsaquo;
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
        <form id="studentForm" method="POST" action="edit.php?id=<?php echo $id; ?>">
            <div class="form-row">
                <div class="form-group">
                    <label for="student_code">Mã sinh viên <span class="required">*</span></label>
                    <input type="text" id="student_code" name="student_code" class="form-control"
                        value="<?php echo htmlspecialchars($student['student_code'], ENT_QUOTES, 'UTF-8'); ?>">
                </div>
                <div class="form-group">
                    <label for="class_id">Lớp học</label>
                    <select id="class_id" name="class_id" class="form-control">
                        <option value="0">-- Chọn lớp --</option>
                        <?php while ($cls = $classList->fetch_assoc()): ?>
                            <option value="<?php echo $cls['id']; ?>"
                                <?php echo ($student['class_id'] == $cls['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($cls['class_name'], ENT_QUOTES, 'UTF-8'); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="full_name">Họ và tên <span class="required">*</span></label>
                <input type="text" id="full_name" name="full_name" class="form-control"
                    value="<?php echo htmlspecialchars($student['full_name'], ENT_QUOTES, 'UTF-8'); ?>">
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" class="form-control"
                        value="<?php echo htmlspecialchars($student['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                </div>
                <div class="form-group">
                    <label for="phone">Điện thoại</label>
                    <input type="text" id="phone" name="phone" class="form-control"
                        value="<?php echo htmlspecialchars($student['phone'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="gender">Giới tính</label>
                    <select id="gender" name="gender" class="form-control">
                        <option value="Nam"  <?php echo ($student['gender'] === 'Nam')  ? 'selected' : ''; ?>>Nam</option>
                        <option value="Nữ"   <?php echo ($student['gender'] === 'Nữ')   ? 'selected' : ''; ?>>Nữ</option>
                        <option value="Khác" <?php echo ($student['gender'] === 'Khác') ? 'selected' : ''; ?>>Khác</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="birthday">Ngày sinh</label>
                    <input type="date" id="birthday" name="birthday" class="form-control"
                        value="<?php echo htmlspecialchars($student['birthday'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                </div>
            </div>

            <div class="form-group">
                <label for="address">Địa chỉ</label>
                <textarea id="address" name="address" class="form-control" rows="3"><?php echo htmlspecialchars($student['address'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
            </div>

            <div class="form-actions">
                <a href="index.php" class="btn btn-secondary">Huỷ</a>
                <a href="view.php?id=<?php echo $id; ?>" class="btn btn-primary">👁 Xem</a>
                <button type="submit" class="btn btn-warning">💾 Cập nhật</button>
            </div>
        </form>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
