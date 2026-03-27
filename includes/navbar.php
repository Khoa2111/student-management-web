<nav class="navbar">
    <div class="navbar-brand">
        <a href="<?php echo SITE_URL; ?>/index.php">
            <img src="<?php echo SITE_URL; ?>/assets/images/logo.svg" alt="<?php echo SITE_NAME; ?>" height="32" style="vertical-align:middle;">
        </a>
        <button class="navbar-toggle" id="navbarToggle" aria-label="Menu">
            <span></span><span></span><span></span>
        </button>
    </div>
    <?php
    $currentFile   = basename($_SERVER['PHP_SELF']);
    $currentPath   = $_SERVER['PHP_SELF'];
    $inStudents    = strpos($currentPath, '/students/') !== false;
    $inClasses     = strpos($currentPath, '/classes/') !== false;
    $inAuth        = strpos($currentPath, '/auth/') !== false;
    $isHome        = $currentFile === 'index.php' && !$inStudents && !$inClasses && !$inAuth;
    ?>
    <ul class="navbar-menu" id="navbarMenu">
        <li><a href="<?php echo SITE_URL; ?>/index.php"<?php echo $isHome ? ' class="active"' : ''; ?>>Trang chủ</a></li>
        <li><a href="<?php echo SITE_URL; ?>/about.php"<?php echo ($currentFile === 'about.php') ? ' class="active"' : ''; ?>>Giới thiệu</a></li>
        <li><a href="<?php echo SITE_URL; ?>/contact.php"<?php echo ($currentFile === 'contact.php') ? ' class="active"' : ''; ?>>Liên hệ</a></li>
        <?php if (isLoggedIn()): ?>
        <li><a href="<?php echo SITE_URL; ?>/dashboard.php"<?php echo ($currentFile === 'dashboard.php') ? ' class="active"' : ''; ?>>Dashboard</a></li>
        <li><a href="<?php echo SITE_URL; ?>/students/index.php"<?php echo $inStudents ? ' class="active"' : ''; ?>>Sinh viên</a></li>
        <li><a href="<?php echo SITE_URL; ?>/classes/index.php"<?php echo $inClasses ? ' class="active"' : ''; ?>>Lớp học</a></li>
        <li class="navbar-user">
            <span>👤 <?php echo htmlspecialchars($_SESSION['username'] ?? '', ENT_QUOTES, 'UTF-8'); ?></span>
            <a href="<?php echo SITE_URL; ?>/auth/logout.php" class="btn-logout">Đăng xuất</a>
        </li>
        <?php else: ?>
        <li><a href="<?php echo SITE_URL; ?>/auth/login.php"<?php echo ($currentFile === 'login.php') ? ' class="active"' : ''; ?>>Đăng nhập</a></li>
        <li><a href="<?php echo SITE_URL; ?>/auth/register.php"<?php echo ($currentFile === 'register.php') ? ' class="active"' : ''; ?>>Đăng ký</a></li>
        <?php endif; ?>
    </ul>
</nav>
