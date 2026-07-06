<?php
declare(strict_types=1);
require_once dirname(__DIR__, 2) . '/config/config.php';
requireAdmin();
$id = (int)($_GET['id'] ?? 0);
$db = getDB();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    requireCsrf();
    $remark = trim($_POST['admin_remark'] ?? '');
    $stmt = $db->prepare('SELECT t.*, p.property_number FROM ownership_transfers t JOIN properties p ON t.property_id=p.id WHERE t.id=? AND t.status="Pending"');
    $stmt->execute([$id]);
    $transfer = $stmt->fetch();
    if ($transfer) {
        $db->beginTransaction();
        try {
            $db->prepare('UPDATE ownership_transfers SET status="Approved", admin_remark=? WHERE id=?')->execute([$remark, $id]);
            $db->prepare('UPDATE properties SET owner_id=?, status="Active" WHERE id=?')->execute([$transfer['new_owner_id'], $transfer['property_id']]);
            $db->prepare('UPDATE certificates SET status="Cancelled" WHERE property_id=?')->execute([$transfer['property_id']]);
            generateCertificateForProperty((int)$transfer['property_id']);

            $newOwner = $db->prepare('SELECT user_id FROM owners WHERE id = ?');
            $newOwner->execute([$transfer['new_owner_id']]);
            $uid = $newOwner->fetchColumn();
            if ($uid) notifyUser((int)$uid, __('transfers'), 'Transfer approved for ' . $transfer['property_number'], 'success');

            $db->commit();
            logActivity('Transfer Approved', "Transfer #$id");
            flash('success', __('updated_success'));
        } catch (Exception $e) {
            $db->rollBack();
            flash('danger', 'Error approving transfer');
        }
    }
    redirect(BASE_URL . '/admin/transfers/index.php');
}

$stmt = $db->prepare('SELECT t.*, p.property_number FROM ownership_transfers t JOIN properties p ON t.property_id=p.id WHERE t.id=?');
$stmt->execute([$id]);
$transfer = $stmt->fetch();
if (!$transfer) redirect(BASE_URL . '/admin/transfers/index.php');
$pageTitle = __('approve');
$isAdminArea = true;
include APP_ROOT . '/includes/header.php';
?>
<div class="page-header"><h1><?= __('approve') ?> <?= __('transfers') ?></h1></div>
<div class="card"><div class="card-body">
<p>Property: <strong><?= e($transfer['property_number']) ?></strong></p>
<form method="POST"><?= csrfField() ?>
<div class="mb-3"><label class="form-label"><?= __('admin_remark') ?></label><textarea name="admin_remark" class="form-control" rows="3"></textarea></div>
<button class="btn btn-success"><?= __('approve') ?></button> <a href="index.php" class="btn btn-secondary"><?= __('cancel') ?></a>
</form></div></div>
<?php include APP_ROOT . '/includes/footer.php'; ?>
