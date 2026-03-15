<?php
// auth/register.php - Trang đăng ký
require_once '../config/config.php';

if (isLoggedIn()) {
    header('Location: ' . url('/dashboard.php'));
    exit();
}

$pageTitle = 'Đăng ký';
$loadValidate = true;
$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username  = trim($_POST['username']  ?? '');
    $email     = trim($_POST['email']     ?? '');
    $full_name = trim($_POST['full_name'] ?? '');
    $password  = $_POST['password']  ?? '';
    $password2 = $_POST['password2'] ?? '';

    // Validate
    if (strlen($username) < 3) {
        $errors[] = 'Tên đăng nhập phải có ít nhất 3 ký tự.';
    } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
        $errors[] = 'Tên đăng nhập chỉ được chứa chữ cái, số và dấu gạch dưới.';
    }

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Email không hợp lệ.';
    }

    if (strlen($password) < 6) {
        $errors[] = 'Mật khẩu phải có ít nhất 6 ký tự.';
    }

    if ($password !== $password2) {
        $errors[] = 'Mật khẩu xác nhận không khớp.';
    }

    if (empty($errors)) {
        // Kiểm tra username / email đã tồn tại chưa
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->bind_param('ss', $username, $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $errors[] = 'Tên đăng nhập hoặc email đã tồn tại.';
        }
        $stmt->close();
    }

    if (empty($errors)) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (username, email, password, full_name, role) VALUES (?, ?, ?, ?, 'teacher')");
        $stmt->bind_param('ssss', $username, $email, $hashedPassword, $full_name);

        if ($stmt->execute()) {
            $_SESSION['success'] = 'Đăng ký thành công! Vui lòng đăng nhập.';
            header('Location: ' . url('/auth/login.php'));
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

<div class="auth-wrapper">
    <div class="auth-card">
        <h2>📝 Đăng ký</h2>
        <p class="auth-subtitle">Tạo tài khoản mới để sử dụng hệ thống</p>

        <?php if ($errors): ?>
            <div class="alert alert-error">
                <?php foreach ($errors as $e) echo '<div>' . htmlspecialchars($e, ENT_QUOTES, 'UTF-8') . '</div>'; ?>
            </div>
        <?php endif; ?>

        <form id="registerForm" method="POST" action="register.php">
            <div class="form-group">
                <label for="full_name">Họ và tên</label>
                <input type="text" id="full_name" name="full_name" class="form-control"
                    value="<?php echo htmlspecialchars($_POST['full_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                    placeholder="Nguyễn Văn A">
            </div>
            <div class="form-group">
                <label for="username">Tên đăng nhập <span class="required">*</span></label>
                <input type="text" id="username" name="username" class="form-control"
                    value="<?php echo htmlspecialchars($_POST['username'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                    placeholder="Ít nhất 3 ký tự, không có khoảng trắng">
            </div>
            <div class="form-group">
                <label for="email">Email <span class="required">*</span></label>
                <input type="email" id="email" name="email" class="form-control"
                    value="<?php echo htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                    placeholder="email@example.com">
            </div>
            <div class="form-group">
                <label for="password">Mật khẩu <span class="required">*</span></label>
                <input type="password" id="password" name="password" class="form-control"
                    placeholder="Ít nhất 6 ký tự">
            </div>
            <div class="form-group">
                <label for="password2">Xác nhận mật khẩu <span class="required">*</span></label>
                <input type="password" id="password2" name="password2" class="form-control"
                    placeholder="Nhập lại mật khẩu">
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-success" style="width:100%;padding:12px;">Đăng ký</button>
            </div>
        </form>

        <div class="auth-link">
            <p>Đã có tài khoản? <a href="<?php echo url('/auth/login.php'); ?>">Đăng nhập</a></p>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
