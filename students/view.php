<?php
// students/view.php - Xem chi tiết sinh viên
require_once '../config/config.php';
require_once '../auth/check_role.php';
requireRole('admin');

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    header('Location: ' . SITE_URL . '/students/index.php');
    exit();
}

// Lấy thông tin sinh viên + lớp học + tài khoản liên kết
$stmt = $conn->prepare(
    "SELECT s.*, c.class_name, c.class_code,
            u.email AS account_email, u.username AS account_username, u.status AS account_status
     FROM students s
     LEFT JOIN classes c ON s.class_id = c.id
     LEFT JOIN users u ON s.user_id = u.id
     WHERE s.id = ?"
);
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

// Lấy danh sách đăng ký môn học
$enrollments = $conn->prepare(
    "SELECT e.semester, e.grade, sub.subject_code, sub.subject_name, sub.credits
     FROM enrollments e
     JOIN subjects sub ON e.subject_id = sub.id
     WHERE e.student_id = ?
     ORDER BY e.semester DESC, sub.subject_name"
);
$enrollments->bind_param('i', $id);
$enrollments->execute();
$enrollResult = $enrollments->get_result();
$enrollments->close();

$pageTitle = 'Chi tiết: ' . $student['full_name'];

include '../includes/header.php';
include '../includes/navbar.php';
?>

<div class="main-content">
    <div class="page-header">
        <div>
            <h1>👤 <?php echo htmlspecialchars($student['full_name'], ENT_QUOTES, 'UTF-8'); ?></h1>
            <span class="breadcrumb">
                <a href="<?php echo SITE_URL; ?>/dashboard.php">Dashboard</a> &rsaquo;
                <a href="<?php echo SITE_URL; ?>/students/index.php">Sinh viên</a> &rsaquo;
                Chi tiết
            </span>
        </div>
        <div style="display:flex;gap:10px;">
            <a href="edit.php?id=<?php echo $id; ?>" class="btn btn-warning">✏️ Sửa</a>
            <a href="delete.php?id=<?php echo $id; ?>"
               class="btn btn-danger confirm-delete"
               data-name="<?php echo htmlspecialchars($student['full_name'], ENT_QUOTES, 'UTF-8'); ?>">🗑 Xoá</a>
            <a href="index.php" class="btn btn-secondary">← Quay lại</a>
        </div>
    </div>

    <!-- Thông tin cá nhân -->
    <div class="card">
        <div class="card-header">📋 Thông tin cá nhân</div>
        <div class="card-body">
            <div class="student-detail-grid">
                <div>
                    <div class="detail-row">
                        <span class="detail-label">Mã sinh viên:</span>
                        <span class="detail-value"><code><?php echo htmlspecialchars($student['student_code'], ENT_QUOTES, 'UTF-8'); ?></code></span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Họ và tên:</span>
                        <span class="detail-value"><strong><?php echo htmlspecialchars($student['full_name'], ENT_QUOTES, 'UTF-8'); ?></strong></span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Giới tính:</span>
                        <span class="detail-value">
                            <?php if ($student['gender'] === 'Nam'): ?>
                                <span class="badge badge-info">Nam</span>
                            <?php elseif ($student['gender'] === 'Nữ'): ?>
                                <span class="badge badge-warning">Nữ</span>
                            <?php else: ?>
                                <span class="badge badge-primary"><?php echo htmlspecialchars($student['gender'] ?? '', ENT_QUOTES, 'UTF-8'); ?></span>
                            <?php endif; ?>
                        </span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Ngày sinh:</span>
                        <span class="detail-value">
                            <?php echo $student['birthday'] ? date('d/m/Y', strtotime($student['birthday'])) : '—'; ?>
                        </span>
                    </div>
                </div>
                <div>
                    <div class="detail-row">
                        <span class="detail-label">Lớp học:</span>
                        <span class="detail-value">
                            <?php if ($student['class_name']): ?>
                                <?php echo htmlspecialchars($student['class_name'], ENT_QUOTES, 'UTF-8'); ?>
                                <span style="color:#999;font-size:0.85rem;">(<?php echo htmlspecialchars($student['class_code'], ENT_QUOTES, 'UTF-8'); ?>)</span>
                            <?php else: ?>
                                <span style="color:#999;">Chưa xếp lớp</span>
                            <?php endif; ?>
                        </span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Email:</span>
                        <span class="detail-value"><?php echo htmlspecialchars($student['email'] ?? '—', ENT_QUOTES, 'UTF-8'); ?></span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Điện thoại:</span>
                        <span class="detail-value"><?php echo htmlspecialchars($student['phone'] ?? '—', ENT_QUOTES, 'UTF-8'); ?></span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Địa chỉ:</span>
                        <span class="detail-value"><?php echo htmlspecialchars($student['address'] ?? '—', ENT_QUOTES, 'UTF-8'); ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tài khoản liên kết -->
    <div class="card">
        <div class="card-header">🔐 Tài khoản hệ thống</div>
        <div class="card-body">
            <?php if ($student['user_id']): ?>
            <div class="student-detail-grid">
                <div>
                    <div class="detail-row">
                        <span class="detail-label">Tên đăng nhập:</span>
                        <span class="detail-value"><code><?php echo htmlspecialchars($student['account_username'], ENT_QUOTES, 'UTF-8'); ?></code></span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Email tài khoản:</span>
                        <span class="detail-value"><?php echo htmlspecialchars($student['account_email'], ENT_QUOTES, 'UTF-8'); ?></span>
                    </div>
                </div>
                <div>
                    <div class="detail-row">
                        <span class="detail-label">Trạng thái:</span>
                        <span class="detail-value">
                            <?php if (($student['account_status'] ?? 'active') === 'active'): ?>
                                <span class="badge badge-success">Hoạt động</span>
                            <?php else: ?>
                                <span class="badge badge-danger">Vô hiệu hoá</span>
                            <?php endif; ?>
                        </span>
                    </div>
                </div>
            </div>
            <?php else: ?>
            <p style="color:#999;">Sinh viên chưa được liên kết với tài khoản hệ thống.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Môn học đã đăng ký -->
    <div class="card">
        <div class="card-header">📚 Môn học đã đăng ký</div>
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
                        <?php if ($enrollResult->num_rows > 0): ?>
                            <?php $i = 1; while ($row = $enrollResult->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $i++; ?></td>
                                <td><code><?php echo htmlspecialchars($row['subject_code'], ENT_QUOTES, 'UTF-8'); ?></code></td>
                                <td><?php echo htmlspecialchars($row['subject_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo $row['credits']; ?></td>
                                <td><?php echo htmlspecialchars($row['semester'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td>
                                    <?php if ($row['grade'] !== null): ?>
                                        <?php
                                            $grade = floatval($row['grade']);
                                            $cls   = ($grade >= 8) ? 'badge-success' : (($grade >= 5) ? 'badge-warning' : 'badge-danger');
                                        ?>
                                        <span class="badge <?php echo $cls; ?>"><?php echo number_format($grade, 1); ?></span>
                                    <?php else: ?>
                                        <span style="color:#999;">Chưa có</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center" style="padding:30px;color:#999;">
                                    Chưa đăng ký môn học nào.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
