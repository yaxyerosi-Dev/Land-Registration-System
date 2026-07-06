<?php
declare(strict_types=1);
require_once dirname(__DIR__, 2) . '/config/config.php';
requireAdmin();
$db = getDB();
$id = (int)($_GET['id'] ?? 0);
$stmt = $db->prepare('SELECT * FROM properties WHERE id = ?');
$stmt->execute([$id]);
$property = $stmt->fetch();
if (!$property) redirect(BASE_URL . '/admin/properties/index.php');
$owners = $db->query('SELECT id, full_name FROM owners ORDER BY full_name')->fetchAll();
$lands = $db->query('SELECT id, plot_number, land_number FROM lands ORDER BY plot_number')->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    requireCsrf();
    $stmt = $db->prepare('UPDATE properties SET property_number=?, registration_number=?, owner_id=?, land_id=?, ownership_date=?, status=? WHERE id=?');
    $stmt->execute([
        trim($_POST['property_number']), trim($_POST['registration_number']),
        (int)$_POST['owner_id'], (int)$_POST['land_id'],
        $_POST['ownership_date'], $_POST['status'], $id
    ]);
    flash('success', __('updated_success'));
    redirect(BASE_URL . '/admin/properties/index.php');
}
$pageTitle = __('edit') . ' - ' . __('properties');
$isAdminArea = true;
include APP_ROOT . '/includes/header.php';
?>
<div class="page-header"><h1><?= __('edit') ?> <?= __('properties') ?></h1></div>
<div class="card"><div class="card-body"><form method="POST"><?= csrfField() ?>
<div class="row g-3">
<div class="col-md-6"><label class="form-label"><?= __('property_number') ?></label><input name="property_number" class="form-control" required value="<?= e($property['property_number']) ?>"></div>
<div class="col-md-6"><label class="form-label"><?= __('registration_number') ?></label><input name="registration_number" class="form-control" required value="<?= e($property['registration_number']) ?>"></div>
<div class="col-md-6"><label class="form-label"><?= __('select_owner') ?></label><select name="owner_id" class="form-select" required><?php foreach($owners as $o): ?><option value="<?= $o['id'] ?>" <?= $property['owner_id']==$o['id']?'selected':'' ?>><?= e($o['full_name']) ?></option><?php endforeach; ?></select></div>
<div class="col-md-6"><label class="form-label"><?= __('select_land') ?></label><select name="land_id" class="form-select" required><?php foreach($lands as $l): ?><option value="<?= $l['id'] ?>" <?= $property['land_id']==$l['id']?'selected':'' ?>><?= e($l['plot_number']) ?></option><?php endforeach; ?></select></div>
<div class="col-md-6"><label class="form-label"><?= __('ownership_date') ?></label><input type="date" name="ownership_date" class="form-control" value="<?= e($property['ownership_date']) ?>"></div>
<div class="col-md-6"><label class="form-label"><?= __('status') ?></label><select name="status" class="form-select"><?php foreach(['Active','Transferred','Disputed'] as $s): ?><option <?= $property['status']===$s?'selected':'' ?>><?= $s ?></option><?php endforeach; ?></select></div>
</div>
<div class="mt-3"><button class="btn btn-primary"><?= __('save') ?></button> <a href="index.php" class="btn btn-secondary"><?= __('cancel') ?></a></div>
</form></div></div>
<?php include APP_ROOT . '/includes/footer.php'; ?>
