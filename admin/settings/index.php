<?php
declare(strict_types=1);
require_once dirname(__DIR__, 2) . '/config/config.php';
requireAdmin();
$db = getDB();
$settings = getSettings();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    requireCsrf();
    $action = $_POST['action'] ?? 'settings';

    if ($action === 'settings') {
        $logo = !empty($_FILES['logo']['name']) ? uploadFile($_FILES['logo'], 'logos') : $settings['logo'];
        $stmt = $db->prepare('UPDATE settings SET system_name=?, office_name=?, office_address=?, office_phone=?, office_email=?, logo=? WHERE id=?');
        $stmt->execute([
            trim($_POST['system_name']), trim($_POST['office_name']), trim($_POST['office_address']),
            trim($_POST['office_phone']), trim($_POST['office_email']), $logo, $settings['id']
        ]);
        flash('success', __('updated_success'));
    } elseif ($action === 'password') {
        $user = currentUser();
        if (password_verify($_POST['current_password'], $user['password'])) {
            if ($_POST['new_password'] === $_POST['confirm_password']) {
                $hash = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
                $db->prepare('UPDATE users SET password=? WHERE id=?')->execute([$hash, $user['id']]);
                flash('success', __('password_changed'));
            } else {
                flash('danger', __('password_mismatch'));
            }
        } else {
            flash('danger', __('invalid_password'));
        }
    }
    redirect(BASE_URL . '/admin/settings/index.php');
}

$pageTitle = __('settings');
$isAdminArea = true;
include APP_ROOT . '/includes/header.php';
?>
<div class="page-header"><h1><?= __('settings') ?></h1></div>
<div class="row g-3">
<div class="col-lg-7"><div class="card"><div class="card-header"><?= __('system_settings') ?></div><div class="card-body">
<form method="POST" enctype="multipart/form-data"><?= csrfField() ?><input type="hidden" name="action" value="settings">
<div class="mb-3"><label class="form-label">System Name</label><input name="system_name" class="form-control" value="<?= e($settings['system_name']) ?>"></div>
<div class="mb-3"><label class="form-label"><?= __('office_name') ?></label><input name="office_name" class="form-control" value="<?= e($settings['office_name']) ?>"></div>
<div class="mb-3"><label class="form-label"><?= __('office_address') ?></label><textarea name="office_address" class="form-control" rows="2"><?= e($settings['office_address']) ?></textarea></div>
<div class="row g-3"><div class="col-md-6"><label class="form-label"><?= __('office_phone') ?></label><input name="office_phone" class="form-control" value="<?= e($settings['office_phone']) ?>"></div>
<div class="col-md-6"><label class="form-label"><?= __('office_email') ?></label><input name="office_email" class="form-control" value="<?= e($settings['office_email']) ?>"></div></div>
<div class="mb-3 mt-3"><label class="form-label"><?= __('logo') ?></label><input type="file" name="logo" class="form-control" accept="image/*"><?php if($settings['logo']): ?><img src="<?= UPLOAD_URL.e($settings['logo']) ?>" height="40" class="mt-2"><?php endif; ?></div>
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
