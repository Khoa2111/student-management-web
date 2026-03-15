<?php
// index.php - Trang chủ
require_once 'config/config.php';

$pageTitle = 'Trang chủ';

// Lấy số liệu thống kê để hiển thị
$totalStudents = $conn->query("SELECT COUNT(*) FROM students")->fetch_row()[0];
$totalClasses  = $conn->query("SELECT COUNT(*) FROM classes")->fetch_row()[0];
$totalSubjects = $conn->query("SELECT COUNT(*) FROM subjects")->fetch_row()[0];
$totalUsers    = $conn->query("SELECT COUNT(*) FROM users")->fetch_row()[0];

include 'includes/header.php';
include 'includes/navbar.php';
?>

<!-- Hero Section -->
<section class="hero">
    <img src="<?php echo url('/assets/images/hero-illustration.svg'); ?>"
         alt="Student Management System" class="hero-illustration">
    <h1>🎓 Student Management System</h1>
    <p>Hệ thống quản lý sinh viên toàn diện – Dễ sử dụng, hiệu quả, chính xác. Quản lý thông tin sinh viên, lớp học và môn học trên một nền tảng duy nhất.</p>
    <div class="hero-buttons">
        <?php if (isLoggedIn()): ?>
            <a href="<?php echo url('/dashboard.php'); ?>" class="btn-hero-primary">📊 Vào Dashboard</a>
            <a href="<?php echo url('/students/index.php'); ?>" class="btn-hero-outline">👥 Quản lý sinh viên</a>
        <?php else: ?>
            <a href="<?php echo url('/auth/login.php'); ?>" class="btn-hero-primary">🔐 Đăng nhập</a>
            <a href="<?php echo url('/auth/register.php'); ?>" class="btn-hero-outline">📝 Đăng ký</a>
        <?php endif; ?>
    </div>
</section>

<!-- Stats Banner -->
<section class="stats-banner">
    <div class="stats-grid">
        <div class="stat-item">
            <h2><?php echo $totalStudents; ?></h2>
            <p>👥 Sinh viên</p>
        </div>
        <div class="stat-item">
            <h2><?php echo $totalClasses; ?></h2>
            <p>🏫 Lớp học</p>
        </div>
        <div class="stat-item">
            <h2><?php echo $totalSubjects; ?></h2>
            <p>📚 Môn học</p>
        </div>
        <div class="stat-item">
            <h2><?php echo $totalUsers; ?></h2>
            <p>👤 Tài khoản</p>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="features">
    <h2>✨ Tính năng nổi bật</h2>
    <div class="features-grid">
        <div class="feature-card">
            <div class="feature-icon">👥</div>
            <h3>Quản lý sinh viên</h3>
            <p>Thêm, sửa, xoá và xem thông tin chi tiết của từng sinh viên một cách dễ dàng.</p>
        </div>
        <div class="feature-card">
            <div class="feature-icon">🏫</div>
            <h3>Quản lý lớp học</h3>
            <p>Tổ chức sinh viên theo từng lớp học, theo dõi danh sách lớp nhanh chóng.</p>
        </div>
        <div class="feature-card">
            <div class="feature-icon">📚</div>
            <h3>Môn học &amp; Điểm số</h3>
            <p>Quản lý môn học và điểm số của sinh viên qua từng học kỳ.</p>
        </div>
        <div class="feature-card">
            <div class="feature-icon">🔒</div>
            <h3>Bảo mật tài khoản</h3>
            <p>Hệ thống đăng nhập bảo mật với phân quyền người dùng.</p>
        </div>
        <div class="feature-card">
            <div class="feature-icon">📊</div>
            <h3>Dashboard tổng quan</h3>
            <p>Xem nhanh thống kê tổng quan về sinh viên và lớp học.</p>
        </div>
        <div class="feature-card">
            <div class="feature-icon">📱</div>
            <h3>Giao diện responsive</h3>
            <p>Hoạt động tốt trên máy tính, máy tính bảng và điện thoại.</p>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
