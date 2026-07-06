<?php
declare(strict_types=1);
require_once dirname(__DIR__, 2) . '/config/config.php';
requireAdmin();

$db = getDB();
$errors = [];

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
    $errors = validateRequired(['full_name' => __('full_name'), 'national_id' => __('national_id')], $data);

    if (empty($errors)) {
        $photo = !empty($_FILES['photo']['name']) ? uploadFile($_FILES['photo'], 'photos') : null;
        $stmt = $db->prepare('INSERT INTO owners (user_id, full_name, national_id, phone, email, address, photo) VALUES (?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute([$data['user_id'], $data['full_name'], $data['national_id'], $data['phone'], $data['email'], $data['address'], $photo]);
        logActivity('Owner Created', $data['full_name']);
        flash('success', __('saved_success'));
        redirect(BASE_URL . '/admin/owners/index.php');
    }
}

$users = $db->query("SELECT id, full_name, email FROM users WHERE role = 'user' AND status = 'active'")->fetchAll();
$pageTitle = __('add') . ' - ' . __('owners');
$isAdminArea = true;
include APP_ROOT . '/includes/header.php';
?>

<div class="page-header"><h1><?= __('add') ?> <?= __('owners') ?></h1></div>
<div class="card"><div class="card-body">
<form method="POST" enctype="multipart/form-data">
    <?= csrfField() ?>
    <div class="row g-3">
        <div class="col-md-6"><label class="form-label"><?= __('full_name') ?> *</label><input type="text" name="full_name" class="form-control" required value="<?= e($_POST['full_name'] ?? '') ?>"></div>
        <div class="col-md-6"><label class="form-label"><?= __('national_id') ?> *</label><input type="text" name="national_id" class="form-control" required value="<?= e($_POST['national_id'] ?? '') ?>"></div>
        <div class="col-md-6"><label class="form-label"><?= __('phone') ?></label><input type="text" name="phone" class="form-control" value="<?= e($_POST['phone'] ?? '') ?>"></div>
        <div class="col-md-6"><label class="form-label"><?= __('email') ?></label><input type="email" name="email" class="form-control" value="<?= e($_POST['email'] ?? '') ?>"></div>
        <div class="col-12"><label class="form-label"><?= __('address') ?></label><textarea name="address" class="form-control" rows="2"><?= e($_POST['address'] ?? '') ?></textarea></div>
        <div class="col-md-6"><label class="form-label">Link User Account</label>
            <select name="user_id" class="form-select"><option value="">-- None --</option>
            <?php foreach ($users as $u): ?><option value="<?= $u['id'] ?>"><?= e($u['full_name'] . ' (' . $u['email'] . ')') ?></option><?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-6"><label class="form-label">Photo</label><input type="file" name="photo" class="form-control" accept="image/*"></div>
    </div>
    <div class="mt-3"><button type="submit" class="btn btn-primary"><?= __('save') ?></button> <a href="index.php" class="btn btn-secondary"><?= __('cancel') ?></a></div>
</form>
</div></div>

<?php include APP_ROOT . '/includes/footer.php'; ?>
