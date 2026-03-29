<?php
// classes/index.php - Danh sách lớp học (chỉ Admin)
require_once '../config/config.php';
requireRole('admin');

$pageTitle = 'Danh sách lớp học';

$search = trim($_GET['search'] ?? '');

$sql = "SELECT c.id, c.class_code, c.class_name, c.teacher_id,
               COALESCE(u.full_name, c.teacher) AS teacher_name,
               c.description, COUNT(s.id) AS total_students
        FROM classes c
        LEFT JOIN users u ON c.teacher_id = u.id
        LEFT JOIN students s ON s.class_id = c.id";

if ($search !== '') {
    $sql .= " WHERE c.class_code LIKE ? OR c.class_name LIKE ? OR COALESCE(u.full_name, c.teacher) LIKE ?";
}
$sql .= " GROUP BY c.id ORDER BY c.class_name";

if ($search !== '') {
    $like = '%' . $search . '%';
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('sss', $like, $like, $like);
    $stmt->execute();
    $classes = $stmt->get_result();
    $stmt->close();
} else {
    $classes = $conn->query($sql);
}

// Flash message
$flashMsg  = $_SESSION['flash_msg']  ?? '';
$flashType = $_SESSION['flash_type'] ?? 'success';
unset($_SESSION['flash_msg'], $_SESSION['flash_type']);

include '../includes/header.php';
include '../includes/navbar.php';
?>

<div class="main-content">
    <div class="page-header">
        <div>
            <h1>🏫 Danh sách lớp học</h1>
            <span class="breadcrumb"><a href="<?php echo SITE_URL; ?>/dashboard.php">Dashboard</a> &rsaquo; Lớp học</span>
        </div>
        <a href="<?php echo SITE_URL; ?>/classes/add.php" class="btn btn-primary">➕ Thêm lớp học</a>
    </div>

    <?php if ($flashMsg): ?>
        <div class="alert alert-<?php echo $flashType; ?>"><?php echo htmlspecialchars($flashMsg, ENT_QUOTES, 'UTF-8'); ?></div>
    <?php endif; ?>

    <!-- Tìm kiếm -->
    <form method="GET" action="index.php" class="search-bar">
        <div class="form-group">
            <label>🔍 Tìm kiếm</label>
            <input type="text" name="search" class="form-control" placeholder="Mã lớp, tên lớp, giáo viên..."
                value="<?php echo htmlspecialchars($search, ENT_QUOTES, 'UTF-8'); ?>">
        </div>
        <div class="form-group" style="align-self:flex-end">
            <button type="submit" class="btn btn-primary">Tìm kiếm</button>
            <a href="index.php" class="btn btn-secondary">Xoá lọc</a>
        </div>
    </form>

    <!-- Table -->
    <div class="card">
        <div class="card-header">
            📋 Danh sách
            <span style="font-weight:400;font-size:0.85rem;">Tìm thấy <?php echo $classes->num_rows; ?> lớp</span>
        </div>
        <div class="card-body" style="padding:0">
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Mã lớp</th>
                            <th>Tên lớp</th>
                            <th>Giáo viên phụ trách</th>
                            <th>Số sinh viên</th>
                            <th>Mô tả</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($classes->num_rows > 0): ?>
                            <?php $i = 1; while ($row = $classes->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $i++; ?></td>
                                <td><code><?php echo htmlspecialchars($row['class_code'], ENT_QUOTES, 'UTF-8'); ?></code></td>
                                <td><strong><?php echo htmlspecialchars($row['class_name'], ENT_QUOTES, 'UTF-8'); ?></strong></td>
                                <td><?php echo htmlspecialchars($row['teacher_name'] ?? '—', ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><span class="badge badge-primary"><?php echo $row['total_students']; ?> SV</span></td>
                                <td style="max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                                    <?php echo htmlspecialchars($row['description'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
                                </td>
                                <td>
                                    <div class="table-actions">
                                        <a href="<?php echo SITE_URL; ?>/students/index.php?class_id=<?php echo $row['id']; ?>"
                                           class="btn btn-sm btn-primary" title="Xem sinh viên">👥</a>
                                        <a href="edit.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-warning" title="Sửa">✏️</a>
                                        <a href="delete.php?id=<?php echo $row['id']; ?>"
                                           class="btn btn-sm btn-danger confirm-delete"
                                           data-name="<?php echo htmlspecialchars($row['class_name'], ENT_QUOTES, 'UTF-8'); ?>"
                                           title="Xoá">🗑</a>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7">
                                    <div class="empty-state">
                                        <div class="empty-icon">🏫</div>
                                        <h3>Chưa có lớp học nào</h3>
                                        <p>Hãy thêm lớp học mới để bắt đầu.</p>
                                    </div>
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
