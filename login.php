<?php
declare(strict_types=1);
require_once __DIR__ . '/config/config.php';

if (isLoggedIn()) {
    redirect(isAdmin() ? BASE_URL . '/admin/dashboard.php' : BASE_URL . '/user/dashboard.php');
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    requireCsrf();
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email && $password) {
        $db = getDB();
        $stmt = $db->prepare('SELECT * FROM users WHERE email = ? AND status = "active"');
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        if ($user && password_verify($password, $user['password'])) {
            loginUser($user);
            flash('success', __('login_success'));
            redirect($user['role'] === 'admin' ? BASE_URL . '/admin/dashboard.php' : BASE_URL . '/user/dashboard.php');
        }
    }
    $error = __('login_failed');
}

$pageTitle = __('login');
$settings = getSettings();
?>
<!DOCTYPE html>
<html lang="<?= e(getLang()) ?>" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?> - <?= e($settings['system_name']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/assets/css/style.css" rel="stylesheet">
</head>
<body>
<div class="auth-page">
    <div class="auth-card">
        <div class="auth-logo">
            <i class="bi bi-building"></i>
            <h2><?= e($settings['system_name']) ?></h2>
            <p class="text-muted"><?= __('login') ?></p>
        </div>
        <?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
        <?php $flash = getFlash(); if ($flash): ?><div class="alert alert-<?= e($flash['type']) ?>"><?= e($flash['message']) ?></div><?php endif; ?>
        <form method="POST">
            <?= csrfField() ?>
            <div class="mb-3">
                <label class="form-label"><?= __('email') ?></label>
                <input type="email" name="email" class="form-control" required value="<?= e($_POST['email'] ?? '') ?>">
            </div>
            <div class="mb-3">
                <label class="form-label"><?= __('password') ?></label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary w-100 mb-3"><?= __('login') ?></button>
        </form>
        <p class="text-center mb-2"><a href="<?= BASE_URL ?>/register.php"><?= __('create_account') ?></a></p>
        <p class="text-center mb-2"><a href="<?= BASE_URL ?>/verify.php"><?= __('verify_certificate') ?></a></p>
        <p class="text-center mb-0"><a href="<?= BASE_URL ?>/index.php"><i class="bi bi-arrow-left"></i> <?= __('home') ?></a></p>
        <div class="text-center mt-3">
            <a href="?lang=en" class="btn btn-sm btn-outline-secondary">EN</a>
            <a href="?lang=so" class="btn btn-sm btn-outline-secondary">SO</a>
        </div>
    </div>
</div>
<script src="<?= BASE_URL ?>/assets/js/app.js"></script>
</body>
</html>