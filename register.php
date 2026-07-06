<?php
declare(strict_types=1);
require_once __DIR__ . '/config/config.php';

if (isLoggedIn()) {
    redirect(BASE_URL . '/user/dashboard.php');
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    requireCsrf();
    $data = [
        'full_name' => trim($_POST['full_name'] ?? ''),
        'email' => trim($_POST['email'] ?? ''),
        'phone' => trim($_POST['phone'] ?? ''),
        'password' => $_POST['password'] ?? '',
        'confirm_password' => $_POST['confirm_password'] ?? '',
    ];

    $errors = validateRequired([
        'full_name' => __('full_name'),
        'email' => __('email'),
        'password' => __('password'),
    ], $data);

    if ($data['password'] !== $data['confirm_password']) {
        $errors['confirm_password'] = __('password_mismatch');
    }

    if (empty($errors)) {
        $db = getDB();
        $check = $db->prepare('SELECT id FROM users WHERE email = ?');
        $check->execute([$data['email']]);
        if ($check->fetch()) {
            $errors['email'] = 'Email already registered';
        } else {
            $photo = !empty($_FILES['profile_photo']['name']) ? uploadFile($_FILES['profile_photo'], 'photos') : null;
            $hash = password_hash($data['password'], PASSWORD_DEFAULT);
            $stmt = $db->prepare('INSERT INTO users (full_name, email, phone, password, role, profile_photo) VALUES (?, ?, ?, ?, "user", ?)');
            $stmt->execute([$data['full_name'], $data['email'], $data['phone'], $hash, $photo]);
            $userId = (int) $db->lastInsertId();

            $stmt = $db->prepare('INSERT INTO owners (user_id, full_name, national_id, phone, email, address) VALUES (?, ?, ?, ?, ?, ?)');
            $nationalId = 'NID-' . strtoupper(substr(uniqid(), -8));
            $stmt->execute([$userId, $data['full_name'], $nationalId, $data['phone'], $data['email'], '']);

            logActivity('User Registration', 'New user: ' . $data['email']);
            flash('success', __('register_success'));
            redirect(BASE_URL . '/login.php');
        }
    }
}

$pageTitle = __('register');
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
            <i class="bi bi-person-plus"></i>
            <h2><?= __('register') ?></h2>
        </div>
        <form method="POST" enctype="multipart/form-data">
            <?= csrfField() ?>
            <div class="mb-3">
                <label class="form-label"><?= __('full_name') ?></label>
                <input type="text" name="full_name" class="form-control" required value="<?= e($_POST['full_name'] ?? '') ?>">
            </div>
            <div class="mb-3">
                <label class="form-label"><?= __('email') ?></label>
                <input type="email" name="email" class="form-control" required value="<?= e($_POST['email'] ?? '') ?>">
                <?php if (!empty($errors['email'])): ?><div class="text-danger small"><?= e($errors['email']) ?></div><?php endif; ?>
            </div>
            <div class="mb-3">
                <label class="form-label"><?= __('phone') ?></label>
                <input type="text" name="phone" class="form-control" value="<?= e($_POST['phone'] ?? '') ?>">
            </div>
            <div class="mb-3">
                <label class="form-label"><?= __('password') ?></label>
                <input type="password" name="password" class="form-control" required minlength="6">
            </div>
            <div class="mb-3">
                <label class="form-label"><?= __('confirm_password') ?></label>
                <input type="password" name="confirm_password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary w-100 mb-3"><?= __('register') ?></button>
        </form>
        <p class="text-center"><a href="<?= BASE_URL ?>/login.php"><?= __('already_have_account') ?> <?= __('login') ?></a></p>
    </div>
</div>
</body>
</html>
