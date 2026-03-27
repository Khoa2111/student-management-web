<?php
// auth/logout.php - Đăng xuất
require_once '../config/config.php';

session_destroy();
header('Location: ' . SITE_URL . '/auth/login.php');
exit();
