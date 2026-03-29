<footer class="footer">
    <div class="footer-container">
        <div class="footer-section">
            <h3>🎓 <?php echo SITE_NAME; ?></h3>
            <p>Hệ thống quản lý sinh viên hiện đại, dễ sử dụng, giúp nhà trường quản lý thông tin sinh viên hiệu quả.</p>
        </div>
        <div class="footer-section">
            <h4>Liên kết nhanh</h4>
            <ul>
                <li><a href="<?php echo SITE_URL; ?>/index.php">Trang chủ</a></li>
                <li><a href="<?php echo SITE_URL; ?>/about.php">Giới thiệu</a></li>
                <li><a href="<?php echo SITE_URL; ?>/contact.php">Liên hệ</a></li>
                <?php if (isLoggedIn()): ?>
                <li><a href="<?php echo SITE_URL; ?>/dashboard.php">Dashboard</a></li>
                <?php else: ?>
                <li><a href="<?php echo SITE_URL; ?>/auth/login.php">Đăng nhập</a></li>
                <?php endif; ?>
            </ul>
        </div>
        <div class="footer-section">
            <h4>Thông tin liên hệ</h4>
            <ul>
                <li>📍 123 Đường Nguyễn Văn Linh, Quận Ninh Kiều, TP. Cần Thơ</li>
                <li>📞 (0292) 1234 5678</li>
                <li>✉️ info@school.edu.vn</li>
            </ul>
        </div>
    </div>
    <div class="footer-bottom">
        <p>&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. All rights reserved.</p>
    </div>
</footer>

<!-- Nút scroll to top -->
<button id="scrollTopBtn" title="Lên đầu trang">&#8679;</button>

<script src="<?php echo SITE_URL; ?>/js/main.js"></script>
<?php if (!empty($loadValidate)): ?>
<script src="<?php echo SITE_URL; ?>/js/validate.js"></script>
<?php endif; ?>
</body>
</html>
