<?php
// students/index.php - Danh sách sinh viên
require_once '../config/config.php';
requireLogin();

$pageTitle = 'Danh sách sinh viên';
$isAdmin   = hasRole('admin');
$userId    = $_SESSION['user_id'];

// Tìm kiếm và lọc
$search      = trim($_GET['search']   ?? '');
$classFilter = intval($_GET['class_id'] ?? 0);

// Xây dựng câu truy vấn
$where  = [];
$params = [];
$types  = '';

// Teacher: chỉ xem sinh viên trong lớp của mình
if (!$isAdmin) {
    $where[] = "s.class_id IN (SELECT id FROM classes WHERE teacher_id = ?)";
    $params[] = $userId;
    $types   .= 'i';
}

if ($search !== '') {
    $like = '%' . $search . '%';
    $where[] = "(s.student_code LIKE ? OR s.full_name LIKE ? OR s.email LIKE ?)";
    $params = array_merge($params, [$like, $like, $like]);
    $types .= 'sss';
}
if ($classFilter > 0) {
    $where[] = "s.class_id = ?";
    $params[] = $classFilter;
    $types   .= 'i';
}

$whereSQL = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$sql = "SELECT s.id, s.student_code, s.full_name, s.email, s.phone, s.gender, s.birthday, c.class_name
        FROM students s
        LEFT JOIN classes c ON s.class_id = c.id
        $whereSQL
        ORDER BY s.created_at DESC";

if ($params) {
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $students = $stmt->get_result();
    $stmt->close();
} else {
    $students = $conn->query($sql);
}

// Lấy danh sách lớp để filter
if ($isAdmin) {
    $classList = $conn->query("SELECT id, class_name FROM classes ORDER BY class_name");
} else {
    $stmt = $conn->prepare("SELECT id, class_name FROM classes WHERE teacher_id = ? ORDER BY class_name");
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $classList = $stmt->get_result();
    $stmt->close();
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
            <h1>👥 Danh sách sinh viên</h1>
            <span class="breadcrumb"><a href="<?php echo SITE_URL; ?>/dashboard.php">Dashboard</a> &rsaquo; Sinh viên</span>
        </div>
        <?php if ($isAdmin): ?>
        <a href="<?php echo SITE_URL; ?>/students/add.php" class="btn btn-primary">➕ Thêm sinh viên</a>
        <?php endif; ?>
    </div>

    <?php if ($flashMsg): ?>
        <div class="alert alert-<?php echo $flashType; ?>"><?php echo htmlspecialchars($flashMsg, ENT_QUOTES, 'UTF-8'); ?></div>
    <?php endif; ?>

    <!-- Search & Filter -->
    <form method="GET" action="index.php" class="search-bar">
        <div class="form-group">
            <label>🔍 Tìm kiếm</label>
            <input type="text" name="search" class="form-control" placeholder="Mã SV, họ tên, email..."
                value="<?php echo htmlspecialchars($search, ENT_QUOTES, 'UTF-8'); ?>">
        </div>
        <div class="form-group">
            <label>🏫 Lọc theo lớp</label>
            <select name="class_id" class="form-control">
                <option value="0">-- Tất cả lớp --</option>
                <?php while ($cls = $classList->fetch_assoc()): ?>
                    <option value="<?php echo $cls['id']; ?>" <?php echo ($classFilter == $cls['id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($cls['class_name'], ENT_QUOTES, 'UTF-8'); ?>
                    </option>
                <?php endwhile; ?>
            </select>
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
            <span style="font-weight:400;font-size:0.85rem;">
                Tìm thấy <?php echo $students->num_rows; ?> sinh viên
            </span>
        </div>
        <div class="card-body" style="padding:0">
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Mã SV</th>
                            <th>Họ và tên</th>
                            <th>Email</th>
                            <th>Điện thoại</th>
                            <th>Giới tính</th>
                            <th>Lớp</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($students->num_rows > 0): ?>
                            <?php $i = 1; while ($row = $students->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $i++; ?></td>
                                <td><code><?php echo htmlspecialchars($row['student_code'], ENT_QUOTES, 'UTF-8'); ?></code></td>
                                <td><strong><?php echo htmlspecialchars($row['full_name'], ENT_QUOTES, 'UTF-8'); ?></strong></td>
                                <td><?php echo htmlspecialchars($row['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($row['phone'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                <td>
                                    <?php if ($row['gender'] === 'Nam'): ?>
                                        <span class="badge badge-info">Nam</span>
                                    <?php elseif ($row['gender'] === 'Nữ'): ?>
                                        <span class="badge badge-warning">Nữ</span>
                                    <?php else: ?>
                                        <span class="badge badge-primary"><?php echo htmlspecialchars($row['gender'] ?? '', ENT_QUOTES, 'UTF-8'); ?></span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($row['class_name'] ?? 'Chưa xếp lớp', ENT_QUOTES, 'UTF-8'); ?></td>
                                <td>
                                    <div class="table-actions">
                                        <a href="view.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-primary" title="Xem">👁</a>
                                        <?php if ($isAdmin): ?>
                                        <a href="edit.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-warning" title="Sửa">✏️</a>
                                        <a href="delete.php?id=<?php echo $row['id']; ?>"
                                           class="btn btn-sm btn-danger confirm-delete"
                                           data-name="<?php echo htmlspecialchars($row['full_name'], ENT_QUOTES, 'UTF-8'); ?>"
                                           title="Xoá">🗑</a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8">
                                    <div class="empty-state">
                                        <div class="empty-icon">😔</div>
                                        <h3>Không tìm thấy sinh viên nào</h3>
                                        <p>Hãy thêm sinh viên mới hoặc thay đổi điều kiện tìm kiếm.</p>
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
