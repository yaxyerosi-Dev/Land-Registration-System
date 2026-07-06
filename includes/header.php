<?php
declare(strict_types=1);
if (!defined('APP_ROOT')) {
    require_once dirname(__DIR__) . '/config/config.php';
}
$settings = getSettings();
$user = currentUser();
$lang = getLang();
$pageTitle = $pageTitle ?? __('dashboard');
$isAdminArea = $isAdminArea ?? false;
?>
<!DOCTYPE html>
<html lang="<?= e($lang) ?>" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?> - <?= e($settings['system_name'] ?? APP_NAME) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/assets/css/style.css" rel="stylesheet">
</head>
<body>
<div class="app-wrapper">
    <?php if ($user): ?>
        <?php include APP_ROOT . '/includes/sidebar.php'; ?>
    <?php endif; ?>
    <div class="main-content">
        <?php if ($user): ?>
        <nav class="top-navbar">
            <button class="btn btn-link sidebar-toggle d-lg-none" type="button" id="sidebarToggle">
                <i class="bi bi-list fs-4"></i>
            </button>
            <div class="navbar-search d-none d-md-block">
                <form action="<?= BASE_URL ?>/search.php" method="GET" class="d-flex">
                    <input type="text" name="q" class="form-control form-control-sm" placeholder="<?= __('search_placeholder') ?>">
                </form>
            </div>
            <div class="navbar-actions ms-auto d-flex align-items-center gap-2">
                <div class="dropdown">
                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                        <i class="bi bi-translate"></i> <?= $lang === 'so' ? __('somali') : __('english') ?>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="?lang=en">English</a></li>
                        <li><a class="dropdown-item" href="?lang=so">Soomaali</a></li>
                    </ul>
                </div>
                <button class="btn btn-sm btn-outline-secondary" id="themeToggle" title="<?= __('dark_mode') ?>">
                    <i class="bi bi-moon-fill"></i>
                </button>
                <a href="<?= BASE_URL ?>/<?= $isAdminArea ? 'admin' : 'user' ?>/notifications.php" class="btn btn-sm btn-outline-primary position-relative">
                    <i class="bi bi-bell"></i>
                    <?php $unread = getUnreadNotificationCount((int)$user['id']); if ($unread > 0): ?>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"><?= $unread ?></span>
                    <?php endif; ?>
                </a>
                <div class="dropdown">
                    <button class="btn btn-sm btn-primary dropdown-toggle" data-bs-toggle="dropdown">
                        <i class="bi bi-person-circle"></i> <?= e($user['full_name']) ?>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="<?= BASE_URL ?>/<?= $isAdminArea ? 'admin' : 'user' ?>/profile.php"><i class="bi bi-person"></i> <?= __('profile') ?></a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="<?= BASE_URL ?>/logout.php"><i class="bi bi-box-arrow-right"></i> <?= __('logout') ?></a></li>
                    </ul>
                </div>
            </div>
        </nav>
        <?php endif; ?>
        <main class="content-area p-3 p-md-4">
            <?php $flash = getFlash(); if ($flash): ?>
            <div class="alert alert-<?= e($flash['type']) ?> alert-dismissible fade show" role="alert">
                <?= e($flash['message']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>
