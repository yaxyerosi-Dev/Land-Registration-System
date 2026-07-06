<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/config/config.php';
requireLogin();
$db = getDB();
$user = currentUser();
$owner = getOwnerByUserId((int)$user['id']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    requireCsrf();
    $action = $_POST['action'] ?? 'profile';
    if ($action === 'profile') {
        $photo = !empty($_FILES['profile_photo']['name']) ? uploadFile($_FILES['profile_photo'], 'photos') : $user['profile_photo'];
        $db->prepare('UPDATE users SET full_name=?, phone=?, profile_photo=? WHERE id=?')->execute([trim($_POST['full_name']), trim($_POST['phone']), $photo, $user['id']]);
        if ($owner) {
            $db->prepare('UPDATE owners SET full_name=?, phone=?, address=? WHERE user_id=?')->execute([trim($_POST['full_name']), trim($_POST['phone']), trim($_POST['address']??''), $user['id']]);
        }
        flash('success', __('updated_success'));
    } elseif ($action === 'password') {
        if (password_verify($_POST['current_password'], $user['password'])) {
            if ($_POST['new_password'] === $_POST['confirm_password']) {
                $hash = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
                $db->prepare('UPDATE users SET password=? WHERE id=?')->execute([$hash, $user['id']]);
                flash('success', __('password_changed'));
            } else { flash('danger', __('password_mismatch')); }
        } else { flash('danger', __('invalid_password')); }
    }
    redirect(BASE_URL . '/user/profile.php');
}

$pageTitle = __('profile');
$isAdminArea = false;
include APP_ROOT . '/includes/header.php';
?>
<div class="page-header"><h1><?= __('profile') ?></h1></div>
<div class="row g-3">
<div class="col-lg-7"><div class="card"><div class="card-header"><?= __('profile') ?></div><div class="card-body">
<form method="POST" enctype="multipart/form-data"><?= csrfField() ?><input type="hidden" name="action" value="profile">
<div class="mb-3"><label class="form-label"><?= __('full_name') ?></label><input name="full_name" class="form-control" value="<?= e($user['full_name']) ?>"></div>
<div class="mb-3"><label class="form-label"><?= __('email') ?></label><input class="form-control" value="<?= e($user['email']) ?>" disabled></div>
<div class="mb-3"><label class="form-label"><?= __('phone') ?></label><input name="phone" class="form-control" value="<?= e($user['phone']) ?>"></div>
<?php if ($owner): ?><div class="mb-3"><label class="form-label"><?= __('address') ?></label><textarea name="address" class="form-control" rows="2"><?= e($owner['address']) ?></textarea></div><?php endif; ?>
<div class="mb-3"><label class="form-label">Photo</label><input type="file" name="profile_photo" class="form-control" accept="image/*"></div>
<button class="btn btn-primary"><?= __('save') ?></button>
</form></div></div></div>
<div class="col-lg-5"><div class="card"><div class="card-header"><?= __('change_password') ?></div><div class="card-body">
<form method="POST"><?= csrfField() ?><input type="hidden" name="action" value="password">
<div class="mb-3"><label class="form-label"><?= __('current_password') ?></label><input type="password" name="current_password" class="form-control" required></div>
<div class="mb-3"><label class="form-label"><?= __('new_password') ?></label><input type="password" name="new_password" class="form-control" required minlength="6"></div>
<div class="mb-3"><label class="form-label"><?= __('confirm_password') ?></label><input type="password" name="confirm_password" class="form-control" required></div>
<button class="btn btn-warning"><?= __('change_password') ?></button>
</form></div></div></div>
</div>
<?php include APP_ROOT . '/includes/footer.php'; ?>
