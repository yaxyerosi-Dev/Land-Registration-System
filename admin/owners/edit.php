<?php
declare(strict_types=1);
require_once dirname(__DIR__, 2) . '/config/config.php';
requireAdmin();

$id = (int)($_GET['id'] ?? 0);
$db = getDB();
$stmt = $db->prepare('SELECT * FROM owners WHERE id = ?');
$stmt->execute([$id]);
$owner = $stmt->fetch();
if (!$owner) { flash('danger', __('no_records')); redirect(BASE_URL . '/admin/owners/index.php'); }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    requireCsrf();
    $data = [
        'full_name' => trim($_POST['full_name'] ?? ''),
        'national_id' => trim($_POST['national_id'] ?? ''),
        'phone' => trim($_POST['phone'] ?? ''),
        'email' => trim($_POST['email'] ?? ''),
        'address' => trim($_POST['address'] ?? ''),
        'user_id' => !empty($_POST['user_id']) ? (int)$_POST['user_id'] : null,
    ];
    $photo = !empty($_FILES['photo']['name']) ? uploadFile($_FILES['photo'], 'photos') : $owner['photo'];
    $stmt = $db->prepare('UPDATE owners SET user_id=?, full_name=?, national_id=?, phone=?, email=?, address=?, photo=? WHERE id=?');
    $stmt->execute([$data['user_id'], $data['full_name'], $data['national_id'], $data['phone'], $data['email'], $data['address'], $photo, $id]);
    logActivity('Owner Updated', $data['full_name']);
    flash('success', __('updated_success'));
    redirect(BASE_URL . '/admin/owners/index.php');
}

$users = $db->query("SELECT id, full_name, email FROM users WHERE role = 'user' AND status = 'active'")->fetchAll();
$pageTitle = __('edit') . ' - ' . __('owners');
$isAdminArea = true;
include APP_ROOT . '/includes/header.php';
?>

<div class="page-header"><h1><?= __('edit') ?> <?= __('owners') ?></h1></div>
<div class="card"><div class="card-body">
<form method="POST" enctype="multipart/form-data">
    <?= csrfField() ?>
    <div class="row g-3">
        <div class="col-md-6"><label class="form-label"><?= __('full_name') ?> *</label><input type="text" name="full_name" class="form-control" required value="<?= e($owner['full_name']) ?>"></div>
        <div class="col-md-6"><label class="form-label"><?= __('national_id') ?> *</label><input type="text" name="national_id" class="form-control" required value="<?= e($owner['national_id']) ?>"></div>
        <div class="col-md-6"><label class="form-label"><?= __('phone') ?></label><input type="text" name="phone" class="form-control" value="<?= e($owner['phone']) ?>"></div>
        <div class="col-md-6"><label class="form-label"><?= __('email') ?></label><input type="email" name="email" class="form-control" value="<?= e($owner['email']) ?>"></div>
        <div class="col-12"><label class="form-label"><?= __('address') ?></label><textarea name="address" class="form-control" rows="2"><?= e($owner['address']) ?></textarea></div>
        <div class="col-md-6"><label class="form-label">Link User Account</label>
            <select name="user_id" class="form-select"><option value="">-- None --</option>
            <?php foreach ($users as $u): ?><option value="<?= $u['id'] ?>" <?= $owner['user_id'] == $u['id'] ? 'selected' : '' ?>><?= e($u['full_name']) ?></option><?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-6"><label class="form-label">Photo</label><input type="file" name="photo" class="form-control" accept="image/*"></div>
    </div>
    <div class="mt-3"><button type="submit" class="btn btn-primary"><?= __('save') ?></button> <a href="index.php" class="btn btn-secondary"><?= __('cancel') ?></a></div>
</form>
</div></div>

<?php include APP_ROOT . '/includes/footer.php'; ?>
