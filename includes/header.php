<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' - ' . SITE_NAME : SITE_NAME; ?></title>
    <link rel="stylesheet" href="<?php echo url('/css/base.css'); ?>">
    <link rel="stylesheet" href="<?php echo url('/css/style.css'); ?>">
    <?php if (!empty($loadDashboardCSS)): ?>
    <link rel="stylesheet" href="<?php echo url('/css/dashboard.css'); ?>">
    <?php endif; ?>
</head>
<body>
