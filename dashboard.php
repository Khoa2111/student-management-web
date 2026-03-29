<?php
// dashboard.php - Trang tổng quan
require_once 'config/config.php';
requireLogin();

$pageTitle = 'Dashboard';
$loadDashboardCSS = true;

$isAdmin   = hasRole('admin');
$userId    = $_SESSION['user_id'];

if ($isAdmin) {
    // Admin: thống kê toàn bộ hệ thống
    $totalStudents    = $conn->query("SELECT COUNT(*) FROM students")->fetch_row()[0];
    $totalClasses     = $conn->query("SELECT COUNT(*) FROM classes")->fetch_row()[0];
    $totalSubjects    = $conn->query("SELECT COUNT(*) FROM subjects")->fetch_row()[0];
    $totalEnrollments = $conn->query("SELECT COUNT(*) FROM enrollments")->fetch_row()[0];

    $recentStudents = $conn->query(
        "SELECT s.student_code, s.full_name, s.email, s.gender, c.class_name, s.created_at
         FROM students s
         LEFT JOIN classes c ON s.class_id = c.id
         ORDER BY s.created_at DESC
         LIMIT 5"
    );

    $classStats = $conn->query(
        "SELECT c.class_name, c.class_code, COUNT(s.id) AS total_students
         FROM classes c
         LEFT JOIN students s ON s.class_id = c.id
         GROUP BY c.id
         ORDER BY total_students DESC"
    );
} else {
    // Teacher: thống kê lớp của mình
    $stmt = $conn->prepare(
        "SELECT c.id, c.class_code, c.class_name, COUNT(s.id) AS total_students
         FROM classes c
         LEFT JOIN students s ON s.class_id = c.id
         WHERE c.teacher_id = ?
         GROUP BY c.id"
    );
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $myClasses = $stmt->get_result();
    $stmt->close();

    // Lấy danh sách class_id của giáo viên
    $myClassIds = [];
    $myClassRows = [];
    while ($row = $myClasses->fetch_assoc()) {
        $myClassIds[]  = $row['id'];
        $myClassRows[] = $row;
    }
    $totalStudents = array_sum(array_column($myClassRows, 'total_students'));

    // Sinh viên mới nhất trong lớp của mình
    $recentStudents = null;
    if (!empty($myClassIds)) {
        $placeholders = implode(',', array_fill(0, count($myClassIds), '?'));
        $types        = str_repeat('i', count($myClassIds));
        $stmt = $conn->prepare(
            "SELECT s.student_code, s.full_name, s.email, s.gender, c.class_name, s.created_at
             FROM students s
             LEFT JOIN classes c ON s.class_id = c.id
             WHERE s.class_id IN ($placeholders)
             ORDER BY s.created_at DESC
             LIMIT 5"
        );
        $stmt->bind_param($types, ...$myClassIds);
        $stmt->execute();
        $recentStudents = $stmt->get_result();
        $stmt->close();
    }
}

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

    <?php $flash = getFlash(); if ($flash): ?>
        <div class="alert alert-<?php echo htmlspecialchars($flash['type'], ENT_QUOTES, 'UTF-8'); ?>">
            <?php echo htmlspecialchars($flash['msg'], ENT_QUOTES, 'UTF-8'); ?>
        </div>
    <?php endif; ?>

    <?php if ($isAdmin): ?>
    <!-- Admin: Stat Cards toàn hệ thống -->
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

    <!-- Admin: Quick Actions -->
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
                            <tr><th>Mã SV</th><th>Họ tên</th><th>Lớp</th></tr>
                        </thead>
                        <tbody>
                            <?php if ($recentStudents && $recentStudents->num_rows > 0): ?>
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
                            <tr><th>Mã lớp</th><th>Tên lớp</th><th>Số SV</th></tr>
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

    <?php else: ?>
    <!-- Teacher: thống kê lớp của mình -->
    <div class="dashboard-stats">
        <div class="stat-card">
            <div class="stat-card-icon">👥</div>
            <div class="stat-card-info">
                <h3><?php echo $totalStudents; ?></h3>
                <p>Sinh viên của tôi</p>
            </div>
        </div>
        <div class="stat-card green">
            <div class="stat-card-icon">🏫</div>
            <div class="stat-card-info">
                <h3><?php echo count($myClassRows); ?></h3>
                <p>Lớp phụ trách</p>
            </div>
        </div>
    </div>

    <!-- Teacher: Quick Actions -->
    <div class="card" style="margin-bottom:24px;">
        <div class="card-header">⚡ Thao tác nhanh</div>
        <div class="card-body" style="display:flex;gap:12px;flex-wrap:wrap;">
            <a href="<?php echo SITE_URL; ?>/students/index.php" class="btn btn-primary">👥 Danh sách sinh viên lớp tôi</a>
        </div>
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;flex-wrap:wrap;">
        <!-- Danh sách lớp của giáo viên -->
        <div class="card">
            <div class="card-header">🏫 Lớp của tôi</div>
            <div class="card-body" style="padding:0">
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr><th>Mã lớp</th><th>Tên lớp</th><th>Số SV</th></tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($myClassRows)): ?>
                                <?php foreach ($myClassRows as $row): ?>
                                <tr>
                                    <td><code><?php echo htmlspecialchars($row['class_code'], ENT_QUOTES, 'UTF-8'); ?></code></td>
                                    <td><?php echo htmlspecialchars($row['class_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><span class="badge badge-primary"><?php echo $row['total_students']; ?></span></td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="3" class="text-center" style="padding:20px;color:#999;">Chưa được gán lớp nào.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Sinh viên mới nhất trong lớp của mình -->
        <div class="card">
            <div class="card-header">
                🆕 Sinh viên mới nhất
                <a href="<?php echo SITE_URL; ?>/students/index.php" style="font-size:0.82rem;color:#aaa;font-weight:400;">Xem tất cả &rsaquo;</a>
            </div>
            <div class="card-body" style="padding:0">
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr><th>Mã SV</th><th>Họ tên</th><th>Lớp</th></tr>
                        </thead>
                        <tbody>
                            <?php if ($recentStudents && $recentStudents->num_rows > 0): ?>
                                <?php while ($row = $recentStudents->fetch_assoc()): ?>
                                <tr>
                                    <td><code><?php echo htmlspecialchars($row['student_code'], ENT_QUOTES, 'UTF-8'); ?></code></td>
                                    <td><?php echo htmlspecialchars($row['full_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?php echo htmlspecialchars($row['class_name'] ?? 'Chưa xếp lớp', ENT_QUOTES, 'UTF-8'); ?></td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="3" class="text-center" style="padding:20px;color:#999;">Chưa có sinh viên nào trong lớp của bạn.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>

