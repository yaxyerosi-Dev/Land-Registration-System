<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/config/config.php';
requireUser();
$db = getDB();
$owner = getOwnerByUserId((int)currentUser()['id']);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $owner) {
    requireCsrf();
    $propertyId = (int)($_POST['property_id'] ?? 0);
    $newOwnerId = (int)($_POST['new_owner_id'] ?? 0);
    $reason = trim($_POST['transfer_reason'] ?? '');

    $check = $db->prepare('SELECT id FROM properties WHERE id = ? AND owner_id = ? AND status = "Active"');
    $check->execute([$propertyId, $owner['id']]);
    if ($check->fetch() && $newOwnerId && $newOwnerId !== $owner['id']) {
        $stmt = $db->prepare('INSERT INTO ownership_transfers (property_id, current_owner_id, new_owner_id, transfer_reason, status) VALUES (?,?,?,?,"Pending")');
        $stmt->execute([$propertyId, $owner['id'], $newOwnerId, $reason]);
        logActivity('Transfer Requested', "Property ID $propertyId");
        flash('success', __('saved_success'));
    }
    redirect(BASE_URL . '/user/transfers.php');
}

$properties = []; $owners = []; $history = [];
if ($owner) {
    $stmt = $db->prepare('SELECT id, property_number FROM properties WHERE owner_id = ? AND status = "Active"');
    $stmt->execute([$owner['id']]);
    $properties = $stmt->fetchAll();
    $stmt = $db->prepare('SELECT id, full_name FROM owners WHERE id != ? ORDER BY full_name');
    $stmt->execute([$owner['id']]);
    $owners = $stmt->fetchAll();
    $stmt = $db->prepare('SELECT t.*, p.property_number, no.full_name AS new_owner FROM ownership_transfers t JOIN properties p ON t.property_id=p.id JOIN owners no ON t.new_owner_id=no.id WHERE t.current_owner_id = ? OR t.new_owner_id = ? ORDER BY t.id DESC');
    $stmt->execute([$owner['id'], $owner['id']]);
    $history = $stmt->fetchAll();
}
$pageTitle = __('transfers');
$isAdminArea = false;
include APP_ROOT . '/includes/header.php';
?>
<div class="page-header"><h1><?= __('transfers') ?></h1></div>
<?php if (!empty($properties)): ?>
<div class="card mb-4"><div class="card-header"><?= __('request_transfer') ?></div><div class="card-body">
<form method="POST"><?= csrfField() ?>
<div class="row g-3">
<div class="col-md-4"><label class="form-label"><?= __('select_property') ?></label><select name="property_id" class="form-select" required><?php foreach($properties as $p): ?><option value="<?= $p['id'] ?>"><?= e($p['property_number']) ?></option><?php endforeach; ?></select></div>
<div class="col-md-4"><label class="form-label"><?= __('new_owner') ?></label><select name="new_owner_id" class="form-select" required><?php foreach($owners as $o): ?><option value="<?= $o['id'] ?>"><?= e($o['full_name']) ?></option><?php endforeach; ?></select></div>
<div class="col-md-4"><label class="form-label"><?= __('transfer_reason') ?></label><input name="transfer_reason" class="form-control" required></div>
</div>
<div class="mt-3"><button class="btn btn-primary"><?= __('request_transfer') ?></button></div>
</form></div></div>
<?php endif; ?>
<div class="card"><div class="card-header"><?= __('transfer_history') ?></div><div class="table-responsive"><table class="table table-hover mb-0">
<thead><tr><th>Property</th><th><?= __('new_owner') ?></th><th><?= __('status') ?></th><th>Date</th></tr></thead>
<tbody>
<?php foreach ($history as $h): ?>
<tr><td><?= e($h['property_number']) ?></td><td><?= e($h['new_owner']) ?></td><td><span class="badge bg-<?= $h['status']==='Pending'?'warning':($h['status']==='Approved'?'success':'danger') ?>"><?= e($h['status']) ?></span></td><td><?= e($h['transfer_date']) ?></td></tr>
<?php endforeach; ?>
<?php if(empty($history)): ?><tr><td colspan="4" class="text-center text-muted py-4"><?= __('no_records') ?></td></tr><?php endif; ?>
</tbody></table></div></div>
<?php include APP_ROOT . '/includes/footer.php'; ?>
