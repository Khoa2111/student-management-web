<?php
// students/add.php - Thêm sinh viên mới
require_once '../config/config.php';
requireLogin();

$pageTitle = 'Thêm sinh viên';
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

    // Validate
    if (empty($student_code)) $errors[] = 'Mã sinh viên không được để trống.';
    if (empty($full_name))    $errors[] = 'Họ và tên không được để trống.';
    if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Email không hợp lệ.';
    if ($birthday && !DateTime::createFromFormat('Y-m-d', $birthday)) $errors[] = 'Ngày sinh không hợp lệ.';

    if (empty($errors)) {
        // Kiểm tra mã SV đã tồn tại
        $stmt = $conn->prepare("SELECT id FROM students WHERE student_code = ?");
        $stmt->bind_param('s', $student_code);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) $errors[] = 'Mã sinh viên đã tồn tại.';
        $stmt->close();

        // Kiểm tra email đã tồn tại
        if ($email) {
            $stmt = $conn->prepare("SELECT id FROM students WHERE email = ?");
            $stmt->bind_param('s', $email);
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
            "INSERT INTO students (student_code, full_name, email, phone, gender, birthday, address, class_id)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->bind_param('sssssssi', $student_code, $full_name, $emailVal, $phone, $gender, $birthdayVal, $address, $classVal);

        if ($stmt->execute()) {
            $_SESSION['flash_msg']  = 'Thêm sinh viên thành công!';
            $_SESSION['flash_type'] = 'success';
            header('Location: ' . SITE_URL . '/students/index.php');
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
            <h1>➕ Thêm sinh viên</h1>
            <span class="breadcrumb">
                <a href="<?php echo SITE_URL; ?>/dashboard.php">Dashboard</a> &rsaquo;
                <a href="<?php echo SITE_URL; ?>/students/index.php">Sinh viên</a> &rsaquo;
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
        <form id="studentForm" method="POST" action="add.php">
            <div class="form-row">
                <div class="form-group">
                    <label for="student_code">Mã sinh viên <span class="required">*</span></label>
                    <input type="text" id="student_code" name="student_code" class="form-control"
                        value="<?php echo htmlspecialchars($_POST['student_code'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                        placeholder="VD: SV001">
                </div>
                <div class="form-group">
                    <label for="class_id">Lớp học</label>
                    <select id="class_id" name="class_id" class="form-control">
                        <option value="0">-- Chọn lớp --</option>
                        <?php while ($cls = $classList->fetch_assoc()): ?>
                            <option value="<?php echo $cls['id']; ?>"
                                <?php echo (isset($_POST['class_id']) && $_POST['class_id'] == $cls['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($cls['class_name'], ENT_QUOTES, 'UTF-8'); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="full_name">Họ và tên <span class="required">*</span></label>
                <input type="text" id="full_name" name="full_name" class="form-control"
                    value="<?php echo htmlspecialchars($_POST['full_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                    placeholder="Nguyễn Văn A">
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" class="form-control"
                        value="<?php echo htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                        placeholder="email@example.com">
                </div>
                <div class="form-group">
                    <label for="phone">Điện thoại</label>
                    <input type="text" id="phone" name="phone" class="form-control"
                        value="<?php echo htmlspecialchars($_POST['phone'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                        placeholder="0901234567">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="gender">Giới tính</label>
                    <select id="gender" name="gender" class="form-control">
                        <option value="Nam"  <?php echo (($_POST['gender'] ?? 'Nam') === 'Nam')  ? 'selected' : ''; ?>>Nam</option>
                        <option value="Nữ"   <?php echo (($_POST['gender'] ?? '') === 'Nữ')   ? 'selected' : ''; ?>>Nữ</option>
                        <option value="Khác" <?php echo (($_POST['gender'] ?? '') === 'Khác') ? 'selected' : ''; ?>>Khác</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="birthday">Ngày sinh</label>
                    <input type="date" id="birthday" name="birthday" class="form-control"
                        value="<?php echo htmlspecialchars($_POST['birthday'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                </div>
            </div>

            <div class="form-group">
                <label for="address">Địa chỉ</label>
                <textarea id="address" name="address" class="form-control" rows="3"
                    placeholder="Địa chỉ thường trú"><?php echo htmlspecialchars($_POST['address'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
            </div>

            <div class="form-actions">
                <a href="index.php" class="btn btn-secondary">Huỷ</a>
                <button type="submit" class="btn btn-primary">💾 Lưu sinh viên</button>
            </div>
        </form>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
