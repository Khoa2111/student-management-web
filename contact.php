<?php
// contact.php - Trang liên hệ
require_once 'config/config.php';

$pageTitle = 'Liên hệ';
$loadValidate = true;
$successMsg = '';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = trim($_POST['contact_name'] ?? '');
    $email   = trim($_POST['contact_email'] ?? '');
    $subject = trim($_POST['contact_subject'] ?? '');
    $message = trim($_POST['contact_message'] ?? '');

    if (empty($name))    $errors[] = 'Vui lòng nhập họ tên.';
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL))
        $errors[] = 'Vui lòng nhập email hợp lệ.';
    if (empty($message)) $errors[] = 'Vui lòng nhập nội dung tin nhắn.';

    if (empty($errors)) {
        // Trong thực tế sẽ gửi email, ở đây chỉ hiển thị thông báo thành công
        $successMsg = 'Cảm ơn bạn đã liên hệ! Chúng tôi sẽ phản hồi trong thời gian sớm nhất.';
    }
}

include 'includes/header.php';
include 'includes/navbar.php';
?>

<div class="main-content">
    <div class="page-header">
        <div>
            <h1>📞 Liên hệ</h1>
            <span class="breadcrumb"><a href="<?php echo SITE_URL; ?>/index.php">Trang chủ</a> &rsaquo; Liên hệ</span>
        </div>
    </div>

    <?php if ($successMsg): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($successMsg, ENT_QUOTES, 'UTF-8'); ?></div>
    <?php endif; ?>
    <?php if ($errors): ?>
        <div class="alert alert-error">
            <?php foreach ($errors as $e) echo '<div>' . htmlspecialchars($e, ENT_QUOTES, 'UTF-8') . '</div>'; ?>
        </div>
    <?php endif; ?>

    <div class="contact-grid">
        <!-- Thông tin liên hệ -->
        <div class="contact-info-card">
            <h3>📬 Thông tin liên hệ</h3>
            <div class="contact-info-item">
                <span class="icon">📍</span>
                <div>
                    <strong>Địa chỉ</strong>
                    <p>123 Đường Nguyễn Văn Linh, Quận Ninh Kiều, TP. Cần Thơ</p>
                </div>
            </div>
            <div class="contact-info-item">
                <span class="icon">📞</span>
                <div>
                    <strong>Điện thoại</strong>
                    <p>(0292) 1234 5678</p>
                </div>
            </div>
            <div class="contact-info-item">
                <span class="icon">✉️</span>
                <div>
                    <strong>Email</strong>
                    <p>info@school.edu.vn</p>
                </div>
            </div>
            <div class="contact-info-item">
                <span class="icon">🕐</span>
                <div>
                    <strong>Giờ làm việc</strong>
                    <p>Thứ 2 – Thứ 6: 7:30 – 17:00<br>Thứ 7: 7:30 – 11:30</p>
                </div>
            </div>
        </div>

        <!-- Form liên hệ -->
        <div class="form-container" style="max-width:100%">
            <h3 style="margin-bottom:20px;color:#1a252f;">💬 Gửi tin nhắn</h3>
            <form id="contactForm" method="POST" action="contact.php">
                <div class="form-row">
                    <div class="form-group">
                        <label for="contact_name">Họ và tên <span class="required">*</span></label>
                        <input type="text" id="contact_name" name="contact_name" class="form-control"
                            value="<?php echo htmlspecialchars($_POST['contact_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                            placeholder="Nguyễn Văn A">
                    </div>
                    <div class="form-group">
                        <label for="contact_email">Email <span class="required">*</span></label>
                        <input type="email" id="contact_email" name="contact_email" class="form-control"
                            value="<?php echo htmlspecialchars($_POST['contact_email'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                            placeholder="email@example.com">
                    </div>
                </div>
                <div class="form-group">
                    <label for="contact_subject">Tiêu đề</label>
                    <input type="text" id="contact_subject" name="contact_subject" class="form-control"
                        value="<?php echo htmlspecialchars($_POST['contact_subject'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                        placeholder="Tiêu đề tin nhắn">
                </div>
                <div class="form-group">
                    <label for="contact_message">Nội dung <span class="required">*</span></label>
                    <textarea id="contact_message" name="contact_message" class="form-control" rows="6"
                        placeholder="Nhập nội dung tin nhắn..."><?php echo htmlspecialchars($_POST['contact_message'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">📤 Gửi tin nhắn</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
