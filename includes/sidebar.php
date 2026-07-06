<?php
$base = $isAdminArea ? BASE_URL . '/admin' : BASE_URL . '/user';
$current = basename($_SERVER['PHP_SELF']);
?>
<aside class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <?php $logo = getSettings()['logo'] ?? ''; ?>
        <?php if ($logo): ?>
            <img src="<?= UPLOAD_URL . e($logo) ?>" alt="Logo" class="sidebar-logo">
        <?php else: ?>
            <i class="bi bi-building"></i>
        <?php endif; ?>
        <span><?= e(getSettings()['system_name'] ?? APP_NAME) ?></span>
    </div>
    <ul class="sidebar-nav">
        <li><a href="<?= $base ?>/dashboard.php" class="<?= $current === 'dashboard.php' ? 'active' : '' ?>"><i class="bi bi-speedometer2"></i> <?= __('dashboard') ?></a></li>
        <?php if ($isAdminArea): ?>
        <li><a href="<?= BASE_URL ?>/admin/users/index.php" class="<?= str_contains($_SERVER['PHP_SELF'], '/users/') ? 'active' : '' ?>"><i class="bi bi-people"></i> <?= __('users') ?></a></li>
        <li><a href="<?= BASE_URL ?>/admin/owners/index.php" class="<?= str_contains($_SERVER['PHP_SELF'], '/owners/') ? 'active' : '' ?>"><i class="bi bi-person-badge"></i> <?= __('owners') ?></a></li>
        <li><a href="<?= BASE_URL ?>/admin/lands/index.php" class="<?= str_contains($_SERVER['PHP_SELF'], '/lands/') ? 'active' : '' ?>"><i class="bi bi-map"></i> <?= __('lands') ?></a></li>
        <li><a href="<?= BASE_URL ?>/admin/properties/index.php" class="<?= str_contains($_SERVER['PHP_SELF'], '/properties/') ? 'active' : '' ?>"><i class="bi bi-house-door"></i> <?= __('properties') ?></a></li>
        <li><a href="<?= BASE_URL ?>/admin/transfers/index.php" class="<?= str_contains($_SERVER['PHP_SELF'], '/transfers/') ? 'active' : '' ?>"><i class="bi bi-arrow-left-right"></i> <?= __('transfers') ?></a></li>
        <li><a href="<?= BASE_URL ?>/admin/certificates/index.php" class="<?= str_contains($_SERVER['PHP_SELF'], '/certificates/') ? 'active' : '' ?>"><i class="bi bi-award"></i> <?= __('certificates') ?></a></li>
        <li><a href="<?= BASE_URL ?>/admin/reports/index.php" class="<?= str_contains($_SERVER['PHP_SELF'], '/reports/') ? 'active' : '' ?>"><i class="bi bi-file-earmark-bar-graph"></i> <?= __('reports') ?></a></li>
        <li><a href="<?= BASE_URL ?>/admin/notifications/index.php" class="<?= str_contains($_SERVER['PHP_SELF'], '/notifications/') ? 'active' : '' ?>"><i class="bi bi-megaphone"></i> <?= __('send_notification') ?></a></li>
        <li><a href="<?= BASE_URL ?>/admin/settings/index.php" class="<?= str_contains($_SERVER['PHP_SELF'], '/settings/') ? 'active' : '' ?>"><i class="bi bi-gear"></i> <?= __('settings') ?></a></li>
        <li><a href="<?= BASE_URL ?>/admin/audit_logs.php"><i class="bi bi-journal-text"></i> <?= __('audit_logs') ?></a></li>
        <?php else: ?>
        <li><a href="<?= BASE_URL ?>/user/properties.php" class="<?= $current === 'properties.php' ? 'active' : '' ?>"><i class="bi bi-house-door"></i> <?= __('my_properties') ?></a></li>
        <li><a href="<?= BASE_URL ?>/user/certificates.php" class="<?= $current === 'certificates.php' ? 'active' : '' ?>"><i class="bi bi-award"></i> <?= __('my_certificates') ?></a></li>
        <li><a href="<?= BASE_URL ?>/user/transfers.php" class="<?= $current === 'transfers.php' ? 'active' : '' ?>"><i class="bi bi-arrow-left-right"></i> <?= __('transfers') ?></a></li>
        <li><a href="<?= BASE_URL ?>/user/notifications.php" class="<?= $current === 'notifications.php' ? 'active' : '' ?>"><i class="bi bi-bell"></i> <?= __('notifications') ?></a></li>
        <li><a href="<?= BASE_URL ?>/user/profile.php" class="<?= $current === 'profile.php' ? 'active' : '' ?>"><i class="bi bi-person"></i> <?= __('profile') ?></a></li>
        <?php endif; ?>
        <li><a href="<?= BASE_URL ?>/verify.php"><i class="bi bi-shield-check"></i> <?= __('verify') ?></a></li>
        <li><a href="<?= BASE_URL ?>/search.php"><i class="bi bi-search"></i> <?= __('search') ?></a></li>
    </ul>
</aside>
