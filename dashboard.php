<?php
// dashboard.php - Trang tổng quan
require_once 'config/config.php';
requireLogin();

$pageTitle = 'Dashboard';

// Thống kê tổng quan
$totalStudents   = $conn->query("SELECT COUNT(*) FROM students")->fetch_row()[0];
$totalClasses    = $conn->query("SELECT COUNT(*) FROM classes")->fetch_row()[0];
$totalSubjects   = $conn->query("SELECT COUNT(*) FROM subjects")->fetch_row()[0];
$totalEnrollments = $conn->query("SELECT COUNT(*) FROM enrollments")->fetch_row()[0];

// Sinh viên mới nhất
$recentStudents = $conn->query(
    "SELECT s.student_code, s.full_name, s.email, s.gender, c.class_name, s.created_at
     FROM students s
     LEFT JOIN classes c ON s.class_id = c.id
     ORDER BY s.created_at DESC
     LIMIT 5"
);

// Danh sách lớp và số sinh viên
$classStats = $conn->query(
    "SELECT c.class_name, c.class_code, COUNT(s.id) AS total_students
     FROM classes c
     LEFT JOIN students s ON s.class_id = c.id
     GROUP BY c.id
     ORDER BY total_students DESC"
);

include 'includes/header.php';
include 'includes/navbar.php';
?>

<div class="main-content">
    <div class="page-header">
        <div>
            <h1>📊 Dashboard</h1>
            <span class="breadcrumb">Xin chào, <strong><?php echo htmlspecialchars($_SESSION['full_name'] ?: $_SESSION['username'], ENT_QUOTES, 'UTF-8'); ?></strong>!</span>
        </div>
    </div>

    <!-- Stat Cards -->
    <div class="dashboard-stats">
        <div class="stat-card">
            <div class="stat-card-icon">👥</div>
            <div class="stat-card-info">
                <h3><?php echo $totalStudents; ?></h3>
                <p>Tổng sinh viên</p>
            </div>
        </div>
        <div class="stat-card green">
            <div class="stat-card-icon">🏫</div>
            <div class="stat-card-info">
                <h3><?php echo $totalClasses; ?></h3>
                <p>Tổng lớp học</p>
            </div>
        </div>
        <div class="stat-card orange">
            <div class="stat-card-icon">📚</div>
            <div class="stat-card-info">
                <h3><?php echo $totalSubjects; ?></h3>
                <p>Tổng môn học</p>
            </div>
        </div>
        <div class="stat-card purple">
            <div class="stat-card-icon">📝</div>
            <div class="stat-card-info">
                <h3><?php echo $totalEnrollments; ?></h3>
                <p>Đăng ký môn học</p>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="card" style="margin-bottom:24px;">
        <div class="card-header">⚡ Thao tác nhanh</div>
        <div class="card-body" style="display:flex;gap:12px;flex-wrap:wrap;">
            <a href="<?php echo SITE_URL; ?>/students/add.php" class="btn btn-primary">➕ Thêm sinh viên</a>
            <a href="<?php echo SITE_URL; ?>/classes/add.php"  class="btn btn-success">➕ Thêm lớp học</a>
            <a href="<?php echo SITE_URL; ?>/students/index.php" class="btn btn-secondary">👥 Danh sách sinh viên</a>
            <a href="<?php echo SITE_URL; ?>/classes/index.php"  class="btn btn-secondary">🏫 Danh sách lớp học</a>
        </div>
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;flex-wrap:wrap;">
        <!-- Sinh viên mới nhất -->
        <div class="card">
            <div class="card-header">
                🆕 Sinh viên mới nhất
                <a href="<?php echo SITE_URL; ?>/students/index.php" style="font-size:0.82rem;color:#aaa;font-weight:400;">Xem tất cả &rsaquo;</a>
            </div>
            <div class="card-body" style="padding:0">
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Mã SV</th>
                                <th>Họ tên</th>
                                <th>Lớp</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($recentStudents->num_rows > 0): ?>
                                <?php while ($row = $recentStudents->fetch_assoc()): ?>
                                <tr>
                                    <td><code><?php echo htmlspecialchars($row['student_code'], ENT_QUOTES, 'UTF-8'); ?></code></td>
                                    <td><?php echo htmlspecialchars($row['full_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?php echo htmlspecialchars($row['class_name'] ?? 'Chưa xếp lớp', ENT_QUOTES, 'UTF-8'); ?></td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="3" class="text-center" style="padding:20px;color:#999;">Chưa có sinh viên nào.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Thống kê theo lớp -->
        <div class="card">
            <div class="card-header">
                🏫 Sinh viên theo lớp
                <a href="<?php echo SITE_URL; ?>/classes/index.php" style="font-size:0.82rem;color:#aaa;font-weight:400;">Xem tất cả &rsaquo;</a>
            </div>
            <div class="card-body" style="padding:0">
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Mã lớp</th>
                                <th>Tên lớp</th>
                                <th>Số SV</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($classStats->num_rows > 0): ?>
                                <?php while ($row = $classStats->fetch_assoc()): ?>
                                <tr>
                                    <td><code><?php echo htmlspecialchars($row['class_code'], ENT_QUOTES, 'UTF-8'); ?></code></td>
                                    <td><?php echo htmlspecialchars($row['class_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><span class="badge badge-primary"><?php echo $row['total_students']; ?></span></td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="3" class="text-center" style="padding:20px;color:#999;">Chưa có lớp nào.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
