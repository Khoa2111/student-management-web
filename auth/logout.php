<?php
// auth/logout.php - Đăng xuất
require_once '../config/config.php';

session_destroy();
header('Location: ' . url('/auth/login.php'));
exit();
