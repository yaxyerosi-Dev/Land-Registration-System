<?php
declare(strict_types=1);
require_once dirname(__DIR__, 2) . '/config/config.php';
requireAdmin();
$db = getDB();
$id = (int)($_GET['id'] ?? 0);
$stmt = $db->prepare('SELECT * FROM lands WHERE id = ?');
$stmt->execute([$id]);
$land = $stmt->fetch();
if (!$land) redirect(BASE_URL . '/admin/lands/index.php');
$landTypes = ['Residential','Commercial','Agricultural','Industrial'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    requireCsrf();
    $stmt = $db->prepare('UPDATE lands SET plot_number=?, land_number=?, region=?, district=?, neighborhood=?, full_address=?, land_size=?, land_type=?, status=? WHERE id=?');
    $stmt->execute([
        trim($_POST['plot_number']), trim($_POST['land_number']), trim($_POST['region']),
        trim($_POST['district']), trim($_POST['neighborhood']), trim($_POST['full_address']),
        trim($_POST['land_size']), $_POST['land_type'], $_POST['status'], $id
    ]);
    logActivity('Land Updated', $land['plot_number']);
    flash('success', __('updated_success'));
    redirect(BASE_URL . '/admin/lands/index.php');
}
$pageTitle = __('edit') . ' - ' . __('lands');
$isAdminArea = true;
include APP_ROOT . '/includes/header.php';
?>
<div class="page-header"><h1><?= __('edit') ?> <?= __('lands') ?></h1></div>
<div class="card"><div class="card-body"><form method="POST"><?= csrfField() ?>
<div class="row g-3">
<div class="col-md-6"><label class="form-label"><?= __('plot_number') ?></label><input name="plot_number" class="form-control" required value="<?= e($land['plot_number']) ?>"></div>
<div class="col-md-6"><label class="form-label"><?= __('land_number') ?></label><input name="land_number" class="form-control" required value="<?= e($land['land_number']) ?>"></div>
<div class="col-md-4"><label class="form-label"><?= __('region') ?></label><input name="region" class="form-control" required value="<?= e($land['region']) ?>"></div>
<div class="col-md-4"><label class="form-label"><?= __('district') ?></label><input name="district" class="form-control" required value="<?= e($land['district']) ?>"></div>
<div class="col-md-4"><label class="form-label"><?= __('neighborhood') ?></label><input name="neighborhood" class="form-control" required value="<?= e($land['neighborhood']) ?>"></div>
<div class="col-md-6"><label class="form-label"><?= __('land_size') ?></label><input name="land_size" class="form-control" required value="<?= e($land['land_size']) ?>"></div>
<div class="col-md-6"><label class="form-label"><?= __('land_type') ?></label><select name="land_type" class="form-select"><?php foreach($landTypes as $t): ?><option <?= $land['land_type']===$t?'selected':'' ?>><?= $t ?></option><?php endforeach; ?></select></div>
<div class="col-12"><label class="form-label"><?= __('address') ?></label><textarea name="full_address" class="form-control" rows="2"><?= e($land['full_address']) ?></textarea></div>
<div class="col-md-6"><label class="form-label"><?= __('status') ?></label><select name="status" class="form-select"><?php foreach(['Active','Pending','Transferred'] as $s): ?><option <?= $land['status']===$s?'selected':'' ?>><?= $s ?></option><?php endforeach; ?></select></div>
</div>
<div class="mt-3"><button class="btn btn-primary"><?= __('save') ?></button> <a href="index.php" class="btn btn-secondary"><?= __('cancel') ?></a></div>
</form></div></div>
<?php include APP_ROOT . '/includes/footer.php'; ?>
