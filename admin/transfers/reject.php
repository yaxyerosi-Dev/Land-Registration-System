<?php
declare(strict_types=1);
require_once dirname(__DIR__, 2) . '/config/config.php';
requireAdmin();
$id = (int)($_GET['id'] ?? 0);
$db = getDB();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    requireCsrf();
    $remark = trim($_POST['admin_remark'] ?? '');
    $stmt = $db->prepare('UPDATE ownership_transfers SET status="Rejected", admin_remark=? WHERE id=? AND status="Pending"');
    $stmt->execute([$remark, $id]);
    logActivity('Transfer Rejected', "Transfer #$id");
    flash('success', __('updated_success'));
    redirect(BASE_URL . '/admin/transfers/index.php');
}

$stmt = $db->prepare('SELECT t.*, p.property_number FROM ownership_transfers t JOIN properties p ON t.property_id=p.id WHERE t.id=?');
$stmt->execute([$id]);
$transfer = $stmt->fetch();
if (!$transfer) redirect(BASE_URL . '/admin/transfers/index.php');
$pageTitle = __('reject');
$isAdminArea = true;
include APP_ROOT . '/includes/header.php';
?>
<div class="page-header"><h1><?= __('reject') ?> <?= __('transfers') ?></h1></div>
<div class="card"><div class="card-body">
<p>Property: <strong><?= e($transfer['property_number']) ?></strong></p>
<form method="POST"><?= csrfField() ?>
<div class="mb-3"><label class="form-label"><?= __('admin_remark') ?></label><textarea name="admin_remark" class="form-control" rows="3" required></textarea></div>
<button class="btn btn-danger"><?= __('reject') ?></button> <a href="index.php" class="btn btn-secondary"><?= __('cancel') ?></a>
</form></div></div>
<?php include APP_ROOT . '/includes/footer.php'; ?>
