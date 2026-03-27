<?php
// about.php - Trang giới thiệu
require_once 'config/config.php';

$pageTitle = 'Giới thiệu';
include 'includes/header.php';
include 'includes/navbar.php';
?>

<div class="main-content">
    <div class="page-header">
        <div>
            <h1>📋 Giới thiệu</h1>
            <span class="breadcrumb"><a href="<?php echo SITE_URL; ?>/index.php">Trang chủ</a> &rsaquo; Giới thiệu</span>
        </div>
    </div>

    <div class="about-section">
        <h2>🎓 Về Student Management System</h2>
        <p>
            <strong>Student Management System</strong> là hệ thống quản lý sinh viên được xây dựng bằng PHP thuần, MySQL, HTML và CSS
            – phù hợp để làm đồ án môn Lập trình Web cho sinh viên năm 2.
        </p>
        <p style="margin-top:12px;">
            Hệ thống được thiết kế với kiến trúc rõ ràng, code đơn giản, dễ hiểu và có thể chạy trực tiếp trên XAMPP hoặc Laragon
            mà không cần cài đặt phức tạp.
        </p>

        <div class="about-grid">
            <div class="feature-card">
                <div class="feature-icon">🛠️</div>
                <h3>Công nghệ sử dụng</h3>
                <ul style="margin-top:10px;padding-left:0;list-style:none;color:#555;">
                    <li>✅ PHP thuần (không framework)</li>
                    <li>✅ MySQL / MariaDB</li>
                    <li>✅ HTML5 + CSS3 (Flexbox, Grid)</li>
                    <li>✅ JavaScript cơ bản</li>
                    <li>✅ Tương thích XAMPP / Laragon</li>
                </ul>
            </div>
            <div class="feature-card">
                <div class="feature-icon">📦</div>
                <h3>Chức năng chính</h3>
                <ul style="margin-top:10px;padding-left:0;list-style:none;color:#555;">
                    <li>✅ Đăng ký / Đăng nhập / Đăng xuất</li>
                    <li>✅ Quản lý sinh viên (CRUD)</li>
                    <li>✅ Quản lý lớp học (CRUD)</li>
                    <li>✅ Hiển thị sinh viên theo lớp</li>
                    <li>✅ Dashboard thống kê tổng quan</li>
                </ul>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🗄️</div>
                <h3>Cơ sở dữ liệu</h3>
                <ul style="margin-top:10px;padding-left:0;list-style:none;color:#555;">
                    <li>🔷 Bảng <code>users</code> – tài khoản</li>
                    <li>🔷 Bảng <code>classes</code> – lớp học</li>
                    <li>🔷 Bảng <code>students</code> – sinh viên</li>
                    <li>🔷 Bảng <code>subjects</code> – môn học</li>
                    <li>🔷 Bảng <code>enrollments</code> – đăng ký & điểm</li>
                </ul>
            </div>
            <div class="feature-card">
                <div class="feature-icon">📁</div>
                <h3>Cấu trúc thư mục</h3>
                <ul style="margin-top:10px;padding-left:0;list-style:none;color:#555;font-size:0.88rem;">
                    <li>📂 <code>auth/</code> – Đăng nhập / Đăng ký</li>
                    <li>📂 <code>students/</code> – Quản lý sinh viên</li>
                    <li>📂 <code>classes/</code> – Quản lý lớp học</li>
                    <li>📂 <code>config/</code> – Cấu hình CSDL</li>
                    <li>📂 <code>includes/</code> – Shared components</li>
                    <li>📂 <code>css/ js/</code> – Giao diện & JS</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="about-section">
        <h2>👨‍💻 Mục tiêu giáo dục</h2>
        <p>
            Dự án này được thiết kế để sinh viên hiểu rõ:
        </p>
        <ul style="margin-top:14px;padding-left:20px;color:#555;line-height:2;">
            <li>Cách tổ chức project PHP theo cấu trúc thư mục rõ ràng</li>
            <li>Cách kết nối và thao tác với database MySQL bằng MySQLi</li>
            <li>Cách sử dụng <code>include</code> để tái sử dụng code (header, footer, navbar)</li>
            <li>Cách xử lý form, validate dữ liệu ở server và client</li>
            <li>Cách xây dựng tính năng CRUD cơ bản</li>
            <li>Cách quản lý session để xác thực người dùng</li>
            <li>Cách thiết kế giao diện responsive với CSS Flexbox và Grid</li>
        </ul>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
