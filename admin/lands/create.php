<?php
declare(strict_types=1);
require_once dirname(__DIR__, 2) . '/config/config.php';
requireAdmin();
$db = getDB();
$landTypes = ['Residential','Commercial','Agricultural','Industrial'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    requireCsrf();
    $data = [
        'plot_number' => trim($_POST['plot_number'] ?? ''),
        'land_number' => trim($_POST['land_number'] ?? ''),
        'region' => trim($_POST['region'] ?? ''),
        'district' => trim($_POST['district'] ?? ''),
        'neighborhood' => trim($_POST['neighborhood'] ?? ''),
        'full_address' => trim($_POST['full_address'] ?? ''),
        'land_size' => trim($_POST['land_size'] ?? ''),
        'land_type' => $_POST['land_type'] ?? 'Residential',
        'status' => $_POST['status'] ?? 'Active',
    ];
    $errors = validateRequired([
        'plot_number' => __('plot_number'), 'land_number' => __('land_number'),
        'region' => __('region'), 'district' => __('district'),
        'neighborhood' => __('neighborhood'), 'land_size' => __('land_size'),
    ], $data);
    if (empty($errors)) {
        $stmt = $db->prepare('INSERT INTO lands (plot_number, land_number, region, district, neighborhood, full_address, land_size, land_type, status) VALUES (?,?,?,?,?,?,?,?,?)');
        $stmt->execute(array_values($data));
        logActivity('Land Created', $data['plot_number']);
        flash('success', __('saved_success'));
        redirect(BASE_URL . '/admin/lands/index.php');
    }
}
$pageTitle = __('add') . ' - ' . __('lands');
$isAdminArea = true;
include APP_ROOT . '/includes/header.php';
?>
<div class="page-header"><h1><?= __('add') ?> <?= __('lands') ?></h1></div>
<div class="card"><div class="card-body"><form method="POST"><?= csrfField() ?>
<div class="row g-3">
<div class="col-md-6"><label class="form-label"><?= __('plot_number') ?> *</label><input name="plot_number" class="form-control" required value="<?= e($_POST['plot_number']??'') ?>"></div>
<div class="col-md-6"><label class="form-label"><?= __('land_number') ?> *</label><input name="land_number" class="form-control" required value="<?= e($_POST['land_number']??'') ?>"></div>
<div class="col-md-4"><label class="form-label"><?= __('region') ?> *</label><input name="region" class="form-control" required value="<?= e($_POST['region']??'') ?>"></div>
<div class="col-md-4"><label class="form-label"><?= __('district') ?> *</label><input name="district" class="form-control" required value="<?= e($_POST['district']??'') ?>"></div>
<div class="col-md-4"><label class="form-label"><?= __('neighborhood') ?> *</label><input name="neighborhood" class="form-control" required value="<?= e($_POST['neighborhood']??'') ?>"></div>
<div class="col-md-6"><label class="form-label"><?= __('land_size') ?> *</label><input name="land_size" class="form-control" required value="<?= e($_POST['land_size']??'') ?>"></div>
<div class="col-md-6"><label class="form-label"><?= __('land_type') ?></label><select name="land_type" class="form-select"><?php foreach($landTypes as $t): ?><option><?= $t ?></option><?php endforeach; ?></select></div>
<div class="col-12"><label class="form-label"><?= __('address') ?></label><textarea name="full_address" class="form-control" rows="2"><?= e($_POST['full_address']??'') ?></textarea></div>
<div class="col-md-6"><label class="form-label"><?= __('status') ?></label><select name="status" class="form-select"><option>Active</option><option>Pending</option><option>Transferred</option></select></div>
</div>
<div class="mt-3"><button class="btn btn-primary"><?= __('save') ?></button> <a href="index.php" class="btn btn-secondary"><?= __('cancel') ?></a></div>
</form></div></div>
<?php include APP_ROOT . '/includes/footer.php'; ?>
