<?php
declare(strict_types=1);
require_once dirname(__DIR__, 2) . '/config/config.php';
requireAdmin();
$db = getDB();
$owners = $db->query('SELECT id, full_name FROM owners ORDER BY full_name')->fetchAll();
$lands = $db->query("SELECT id, plot_number, land_number FROM lands WHERE status = 'Active' ORDER BY plot_number")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    requireCsrf();
    $ownerId = (int)($_POST['owner_id'] ?? 0);
    $landId = (int)($_POST['land_id'] ?? 0);
    $propertyNumber = trim($_POST['property_number'] ?? '') ?: generateUniqueNumber('PROP');
    $regNumber = trim($_POST['registration_number'] ?? '') ?: generateUniqueNumber('REG');
    $ownershipDate = $_POST['ownership_date'] ?? date('Y-m-d');

    if ($ownerId && $landId) {
        $stmt = $db->prepare('INSERT INTO properties (property_number, registration_number, owner_id, land_id, ownership_date, status) VALUES (?,?,?,?,?, "Active")');
        $stmt->execute([$propertyNumber, $regNumber, $ownerId, $landId, $ownershipDate]);
        $propertyId = (int) $db->lastInsertId();
        generateCertificateForProperty($propertyId);

        $owner = $db->prepare('SELECT user_id, full_name FROM owners WHERE id = ?');
        $owner->execute([$ownerId]);
        $ownerData = $owner->fetch();
        if ($ownerData && $ownerData['user_id']) {
            notifyUser((int)$ownerData['user_id'], __('ownership_certificate'), "Property $propertyNumber registered. Certificate generated.", 'success');
        }
        logActivity('Property Created', "$propertyNumber - Certificate auto-generated");
        flash('success', __('saved_success'));
        redirect(BASE_URL . '/admin/properties/index.php');
    }
}
$pageTitle = __('add') . ' - ' . __('properties');
$isAdminArea = true;
include APP_ROOT . '/includes/header.php';
?>
<div class="page-header"><h1><?= __('add') ?> <?= __('properties') ?></h1></div>
<div class="card"><div class="card-body"><form method="POST"><?= csrfField() ?>
<div class="row g-3">
<div class="col-md-6"><label class="form-label"><?= __('property_number') ?></label><input name="property_number" class="form-control" placeholder="Auto-generated if empty"></div>
<div class="col-md-6"><label class="form-label"><?= __('registration_number') ?></label><input name="registration_number" class="form-control" placeholder="Auto-generated if empty"></div>
<div class="col-md-6"><label class="form-label"><?= __('select_owner') ?> *</label><select name="owner_id" class="form-select" required><option value="">--</option><?php foreach($owners as $o): ?><option value="<?= $o['id'] ?>"><?= e($o['full_name']) ?></option><?php endforeach; ?></select></div>
<div class="col-md-6"><label class="form-label"><?= __('select_land') ?> *</label><select name="land_id" class="form-select" required><option value="">--</option><?php foreach($lands as $l): ?><option value="<?= $l['id'] ?>"><?= e($l['plot_number'] . ' - ' . $l['land_number']) ?></option><?php endforeach; ?></select></div>
<div class="col-md-6"><label class="form-label"><?= __('ownership_date') ?></label><input type="date" name="ownership_date" class="form-control" value="<?= date('Y-m-d') ?>"></div>
</div>
<p class="text-muted mt-2"><i class="bi bi-info-circle"></i> Certificate will be generated automatically upon save.</p>
<div class="mt-3"><button class="btn btn-primary"><?= __('save') ?></button> <a href="index.php" class="btn btn-secondary"><?= __('cancel') ?></a></div>
</form></div></div>
<?php include APP_ROOT . '/includes/footer.php'; ?>
