<?php
declare(strict_types=1);
require_once dirname(__DIR__, 2) . '/config/config.php';
requireAdmin();
$db = getDB();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    requireCsrf();
    $hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $stmt = $db->prepare('INSERT INTO users (full_name, email, phone, password, role, status) VALUES (?,?,?,?,?,?)');
    $stmt->execute([
        trim($_POST['full_name']), trim($_POST['email']), trim($_POST['phone']),
        $hash, $_POST['role'], $_POST['status']
    ]);
    flash('success', __('saved_success'));
    redirect(BASE_URL . '/admin/users/index.php');
}
$pageTitle = __('add') . ' - ' . __('users');
$isAdminArea = true;
include APP_ROOT . '/includes/header.php';
?>
<div class="page-header"><h1><?= __('add') ?> <?= __('users') ?></h1></div>
<div class="card"><div class="card-body"><form method="POST"><?= csrfField() ?>
<div class="row g-3">
<div class="col-md-6"><label class="form-label"><?= __('full_name') ?></label><input name="full_name" class="form-control" required></div>
<div class="col-md-6"><label class="form-label"><?= __('email') ?></label><input type="email" name="email" class="form-control" required></div>
<div class="col-md-6"><label class="form-label"><?= __('phone') ?></label><input name="phone" class="form-control"></div>
<div class="col-md-6"><label class="form-label"><?= __('password') ?></label><input type="password" name="password" class="form-control" required minlength="6"></div>
<div class="col-md-6"><label class="form-label">Role</label><select name="role" class="form-select"><option value="user">User</option><option value="admin">Admin</option></select></div>
<div class="col-md-6"><label class="form-label"><?= __('status') ?></label><select name="status" class="form-select"><option value="active"><?= __('active') ?></option><option value="inactive"><?= __('inactive') ?></option></select></div>
</div>
<div class="mt-3"><button class="btn btn-primary"><?= __('save') ?></button> <a href="index.php" class="btn btn-secondary"><?= __('cancel') ?></a></div>
</form></div></div>
<?php include APP_ROOT . '/includes/footer.php'; ?>
