<?php
// dashboard.php - Trang tổng quan
require_once 'config/config.php';
requireLogin();

$pageTitle = 'Dashboard';
$loadDashboardCSS = true;

$userRole   = $_SESSION['role']    ?? 'student';
$userId     = $_SESSION['user_id'] ?? 0;

// -------------------------------------------------------
// ADMIN: thống kê toàn hệ thống
// -------------------------------------------------------
if ($userRole === 'admin') {
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
}

// -------------------------------------------------------
// TEACHER: chỉ lớp của mình
// -------------------------------------------------------
if ($userRole === 'teacher') {
    $stmtTeacher = $conn->prepare(
        "SELECT COUNT(DISTINCT s.id) FROM students s
         JOIN classes c ON s.class_id = c.id
         WHERE c.teacher_id = ?"
    );
    $stmtTeacher->bind_param('i', $userId);
    $stmtTeacher->execute();
    $totalStudents = $stmtTeacher->get_result()->fetch_row()[0];
    $stmtTeacher->close();

    $stmtClasses = $conn->prepare(
        "SELECT c.id, c.class_code, c.class_name, COUNT(s.id) AS total_students
         FROM classes c
         LEFT JOIN students s ON s.class_id = c.id
         WHERE c.teacher_id = ?
         GROUP BY c.id
         ORDER BY c.class_name"
    );
    $stmtClasses->bind_param('i', $userId);
    $stmtClasses->execute();
    $myClasses = $stmtClasses->get_result();
    $stmtClasses->close();

    $stmtRecent = $conn->prepare(
        "SELECT s.student_code, s.full_name, s.gender, c.class_name
         FROM students s
         JOIN classes c ON s.class_id = c.id
         WHERE c.teacher_id = ?
         ORDER BY s.created_at DESC
         LIMIT 5"
    );
    $stmtRecent->bind_param('i', $userId);
    $stmtRecent->execute();
    $recentStudents = $stmtRecent->get_result();
    $stmtRecent->close();
}

// -------------------------------------------------------
// STUDENT: thông tin cá nhân của sinh viên
// -------------------------------------------------------
if ($userRole === 'student') {
    $stmtStu = $conn->prepare(
        "SELECT s.*, c.class_name, c.class_code
         FROM students s
         LEFT JOIN classes c ON s.class_id = c.id
         WHERE s.user_id = ?
         LIMIT 1"
    );
    $stmtStu->bind_param('i', $userId);
    $stmtStu->execute();
    $myStudent = $stmtStu->get_result()->fetch_assoc();
    $stmtStu->close();

    if ($myStudent) {
        $stmtEnroll = $conn->prepare(
            "SELECT e.semester, e.grade, sub.subject_code, sub.subject_name, sub.credits
             FROM enrollments e
             JOIN subjects sub ON e.subject_id = sub.id
             WHERE e.student_id = ?
             ORDER BY e.semester DESC, sub.subject_name"
        );
        $stmtEnroll->bind_param('i', $myStudent['id']);
        $stmtEnroll->execute();
        $myEnrollments = $stmtEnroll->get_result();
        $stmtEnroll->close();
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

    <?php
    // Flash message
    $flash = getFlash();
    if ($flash):
    ?>
        <div class="alert alert-<?php echo htmlspecialchars($flash['type'], ENT_QUOTES, 'UTF-8'); ?>">
            <?php echo htmlspecialchars($flash['msg'], ENT_QUOTES, 'UTF-8'); ?>
        </div>
    <?php endif; ?>

    <?php if ($userRole === 'admin'): ?>
    <!-- ======================================================
         ADMIN DASHBOARD
    ====================================================== -->
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
        <div class="card">
            <div class="card-header">
                🆕 Sinh viên mới nhất
                <a href="<?php echo SITE_URL; ?>/students/index.php" style="font-size:0.82rem;color:#aaa;font-weight:400;">Xem tất cả &rsaquo;</a>
            </div>
            <div class="card-body" style="padding:0">
                <div class="table-responsive">
                    <table>
                        <thead><tr><th>Mã SV</th><th>Họ tên</th><th>Lớp</th></tr></thead>
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
        <div class="card">
            <div class="card-header">
                🏫 Sinh viên theo lớp
                <a href="<?php echo SITE_URL; ?>/classes/index.php" style="font-size:0.82rem;color:#aaa;font-weight:400;">Xem tất cả &rsaquo;</a>
            </div>
            <div class="card-body" style="padding:0">
                <div class="table-responsive">
                    <table>
                        <thead><tr><th>Mã lớp</th><th>Tên lớp</th><th>Số SV</th></tr></thead>
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

    <?php elseif ($userRole === 'teacher'): ?>
    <!-- ======================================================
         TEACHER DASHBOARD
    ====================================================== -->
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
                <h3><?php echo $myClasses->num_rows; ?></h3>
                <p>Lớp phụ trách</p>
            </div>
        </div>
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;flex-wrap:wrap;">
        <div class="card">
            <div class="card-header">🏫 Lớp của tôi</div>
            <div class="card-body" style="padding:0">
                <div class="table-responsive">
                    <table>
                        <thead><tr><th>Mã lớp</th><th>Tên lớp</th><th>Số SV</th></tr></thead>
                        <tbody>
                            <?php if ($myClasses->num_rows > 0): ?>
                                <?php while ($row = $myClasses->fetch_assoc()): ?>
                                <tr>
                                    <td><code><?php echo htmlspecialchars($row['class_code'], ENT_QUOTES, 'UTF-8'); ?></code></td>
                                    <td><?php echo htmlspecialchars($row['class_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><span class="badge badge-primary"><?php echo $row['total_students']; ?></span></td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="3" class="text-center" style="padding:20px;color:#999;">Chưa được phân công lớp nào.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header">🆕 Sinh viên mới nhất</div>
            <div class="card-body" style="padding:0">
                <div class="table-responsive">
                    <table>
                        <thead><tr><th>Mã SV</th><th>Họ tên</th><th>Lớp</th></tr></thead>
                        <tbody>
                            <?php if ($recentStudents->num_rows > 0): ?>
                                <?php while ($row = $recentStudents->fetch_assoc()): ?>
                                <tr>
                                    <td><code><?php echo htmlspecialchars($row['student_code'], ENT_QUOTES, 'UTF-8'); ?></code></td>
                                    <td><?php echo htmlspecialchars($row['full_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?php echo htmlspecialchars($row['class_name'] ?? '—', ENT_QUOTES, 'UTF-8'); ?></td>
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
    </div>

    <?php elseif ($userRole === 'student'): ?>
    <!-- ======================================================
         STUDENT DASHBOARD
    ====================================================== -->
    <?php if (!empty($myStudent)): ?>
    <div class="card" style="margin-bottom:24px;">
        <div class="card-header">📋 Thông tin cá nhân</div>
        <div class="card-body">
            <div class="student-detail-grid">
                <div>
                    <div class="detail-row">
                        <span class="detail-label">Mã sinh viên:</span>
                        <span class="detail-value"><code><?php echo htmlspecialchars($myStudent['student_code'], ENT_QUOTES, 'UTF-8'); ?></code></span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Họ và tên:</span>
                        <span class="detail-value"><strong><?php echo htmlspecialchars($myStudent['full_name'], ENT_QUOTES, 'UTF-8'); ?></strong></span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Giới tính:</span>
                        <span class="detail-value">
                            <?php if ($myStudent['gender'] === 'Nam'): ?>
                                <span class="badge badge-info">Nam</span>
                            <?php elseif ($myStudent['gender'] === 'Nữ'): ?>
                                <span class="badge badge-warning">Nữ</span>
                            <?php else: ?>
                                <span class="badge badge-primary"><?php echo htmlspecialchars($myStudent['gender'] ?? '', ENT_QUOTES, 'UTF-8'); ?></span>
                            <?php endif; ?>
                        </span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Ngày sinh:</span>
                        <span class="detail-value"><?php echo $myStudent['birthday'] ? date('d/m/Y', strtotime($myStudent['birthday'])) : '—'; ?></span>
                    </div>
                </div>
                <div>
                    <div class="detail-row">
                        <span class="detail-label">Lớp học:</span>
                        <span class="detail-value">
                            <?php if ($myStudent['class_name']): ?>
                                <?php echo htmlspecialchars($myStudent['class_name'], ENT_QUOTES, 'UTF-8'); ?>
                                <span style="color:#999;font-size:0.85rem;">(<?php echo htmlspecialchars($myStudent['class_code'], ENT_QUOTES, 'UTF-8'); ?>)</span>
                            <?php else: ?>
                                <span style="color:#999;">Chưa xếp lớp</span>
                            <?php endif; ?>
                        </span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Email:</span>
                        <span class="detail-value"><?php echo htmlspecialchars($myStudent['email'] ?? '—', ENT_QUOTES, 'UTF-8'); ?></span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Điện thoại:</span>
                        <span class="detail-value"><?php echo htmlspecialchars($myStudent['phone'] ?? '—', ENT_QUOTES, 'UTF-8'); ?></span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Địa chỉ:</span>
                        <span class="detail-value"><?php echo htmlspecialchars($myStudent['address'] ?? '—', ENT_QUOTES, 'UTF-8'); ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">📚 Môn học đã đăng ký &amp; Điểm số</div>
        <div class="card-body" style="padding:0">
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Mã môn</th>
                            <th>Tên môn học</th>
                            <th>Số tín chỉ</th>
                            <th>Học kỳ</th>
                            <th>Điểm</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($myEnrollments) && $myEnrollments->num_rows > 0): ?>
                            <?php $i = 1; while ($row = $myEnrollments->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $i++; ?></td>
                                <td><code><?php echo htmlspecialchars($row['subject_code'], ENT_QUOTES, 'UTF-8'); ?></code></td>
                                <td><?php echo htmlspecialchars($row['subject_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo intval($row['credits']); ?></td>
                                <td><?php echo htmlspecialchars($row['semester'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td>
                                    <?php if ($row['grade'] !== null): ?>
                                        <?php
                                            $grade = floatval($row['grade']);
                                            $gcls  = ($grade >= 8) ? 'badge-success' : (($grade >= 5) ? 'badge-warning' : 'badge-danger');
                                        ?>
                                        <span class="badge <?php echo $gcls; ?>"><?php echo number_format($grade, 1); ?></span>
                                    <?php else: ?>
                                        <span style="color:#999;">Chưa có</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="6" class="text-center" style="padding:30px;color:#999;">Chưa đăng ký môn học nào.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <?php else: ?>
    <div class="card">
        <div class="card-body" style="text-align:center;padding:40px;color:#999;">
            <div style="font-size:3rem;margin-bottom:12px;">🔗</div>
            <h3>Tài khoản chưa được liên kết với hồ sơ sinh viên</h3>
            <p>Vui lòng liên hệ Admin để liên kết tài khoản với thông tin sinh viên của bạn.</p>
        </div>
    </div>
    <?php endif; ?>

    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
