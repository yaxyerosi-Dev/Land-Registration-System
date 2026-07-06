<?php
declare(strict_types=1);
require_once dirname(__DIR__, 2) . '/config/config.php';
requireAdmin();
$id = (int)($_GET['id'] ?? 0);
$db = getDB();
$stmt = $db->prepare('SELECT * FROM users WHERE id = ?');
$stmt->execute([$id]);
$user = $stmt->fetch();
if (!$user) redirect(BASE_URL . '/admin/users/index.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    requireCsrf();
    if (!empty($_POST['password'])) {
        $hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $stmt = $db->prepare('UPDATE users SET full_name=?, email=?, phone=?, password=?, role=?, status=? WHERE id=?');
        $stmt->execute([trim($_POST['full_name']), trim($_POST['email']), trim($_POST['phone']), $hash, $_POST['role'], $_POST['status'], $id]);
    } else {
        $stmt = $db->prepare('UPDATE users SET full_name=?, email=?, phone=?, role=?, status=? WHERE id=?');
        $stmt->execute([trim($_POST['full_name']), trim($_POST['email']), trim($_POST['phone']), $_POST['role'], $_POST['status'], $id]);
    }
    flash('success', __('updated_success'));
    redirect(BASE_URL . '/admin/users/index.php');
}
$pageTitle = __('edit') . ' - ' . __('users');
$isAdminArea = true;
include APP_ROOT . '/includes/header.php';
?>
<div class="page-header"><h1><?= __('edit') ?> <?= __('users') ?></h1></div>
<div class="card"><div class="card-body"><form method="POST"><?= csrfField() ?>
<div class="row g-3">
<div class="col-md-6"><label class="form-label"><?= __('full_name') ?></label><input name="full_name" class="form-control" required value="<?= e($user['full_name']) ?>"></div>
<div class="col-md-6"><label class="form-label"><?= __('email') ?></label><input type="email" name="email" class="form-control" required value="<?= e($user['email']) ?>"></div>
<div class="col-md-6"><label class="form-label"><?= __('phone') ?></label><input name="phone" class="form-control" value="<?= e($user['phone']) ?>"></div>
<div class="col-md-6"><label class="form-label"><?= __('password') ?> (leave blank to keep)</label><input type="password" name="password" class="form-control" minlength="6"></div>
<div class="col-md-6"><label class="form-label">Role</label><select name="role" class="form-select"><option value="user" <?= $user['role']==='user'?'selected':'' ?>>User</option><option value="admin" <?= $user['role']==='admin'?'selected':'' ?>>Admin</option></select></div>
<div class="col-md-6"><label class="form-label"><?= __('status') ?></label><select name="status" class="form-select"><option value="active" <?= $user['status']==='active'?'selected':'' ?>><?= __('active') ?></option><option value="inactive" <?= $user['status']==='inactive'?'selected':'' ?>><?= __('inactive') ?></option></select></div>
</div>
<div class="mt-3"><button class="btn btn-primary"><?= __('save') ?></button> <a href="index.php" class="btn btn-secondary"><?= __('cancel') ?></a></div>
</form></div></div>
<?php include APP_ROOT . '/includes/footer.php'; ?>
