<?php
// auth/login.php - Trang đăng nhập
require_once '../config/config.php';

// Nếu đã đăng nhập thì chuyển về dashboard
if (isLoggedIn()) {
    header('Location: ' . SITE_URL . '/dashboard.php');
    exit();
}

$pageTitle = 'Đăng nhập';
$loadValidate = true;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($username) || empty($password)) {
        $error = 'Vui lòng điền đầy đủ thông tin.';
    } else {
        $stmt = $conn->prepare("SELECT id, username, email, password, full_name, role FROM users WHERE username = ? OR email = ?");
        $stmt->bind_param('ss', $username, $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $user   = $result->fetch_assoc();
        $stmt->close();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id']   = $user['id'];
            $_SESSION['username']  = $user['username'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['role']      = $user['role'];

            header('Location: ' . SITE_URL . '/dashboard.php');
            exit();
        } else {
            $error = 'Tên đăng nhập hoặc mật khẩu không đúng.';
        }
    }
}

include '../includes/header.php';
include '../includes/navbar.php';
?>

<div class="auth-wrapper">
    <div class="auth-card">
        <h2>🔐 Đăng nhập</h2>
        <p class="auth-subtitle">Chào mừng bạn quay lại hệ thống</p>

        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($_SESSION['success'], ENT_QUOTES, 'UTF-8'); unset($_SESSION['success']); ?></div>
        <?php endif; ?>

        <form id="loginForm" method="POST" action="login.php">
            <div class="form-group">
                <label for="username">Tên đăng nhập / Email <span class="required">*</span></label>
                <input type="text" id="username" name="username" class="form-control"
                    value="<?php echo htmlspecialchars($_POST['username'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                    placeholder="Nhập tên đăng nhập hoặc email" autofocus>
            </div>
            <div class="form-group">
                <label for="password">Mật khẩu <span class="required">*</span></label>
                <input type="password" id="password" name="password" class="form-control"
                    placeholder="Nhập mật khẩu">
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-primary" style="width:100%;padding:12px;">Đăng nhập</button>
            </div>
        </form>

        <div class="auth-link">
            <p>Chưa có tài khoản? <a href="<?php echo SITE_URL; ?>/auth/register.php">Đăng ký ngay</a></p>
            <p style="margin-top:8px;font-size:0.82rem;color:#aaa;">
                Tài khoản demo: <strong>admin</strong> / <strong>admin123</strong>
            </p>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
