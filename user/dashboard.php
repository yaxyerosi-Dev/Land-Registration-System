<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/config/config.php';
requireUser();
$db = getDB();
$owner = getOwnerByUserId((int)currentUser()['id']);
$stats = ['properties' => 0, 'certificates' => 0, 'transfers' => 0, 'notifications' => getUnreadNotificationCount((int)currentUser()['id'])];
if ($owner) {
    $stmt = $db->prepare('SELECT COUNT(*) FROM properties WHERE owner_id = ?');
    $stmt->execute([$owner['id']]);
    $stats['properties'] = (int)$stmt->fetchColumn();
    $stmt = $db->prepare('SELECT COUNT(*) FROM certificates c JOIN properties p ON c.property_id=p.id WHERE p.owner_id = ? AND c.status="Valid"');
    $stmt->execute([$owner['id']]);
    $stats['certificates'] = (int)$stmt->fetchColumn();
    $stmt = $db->prepare('SELECT COUNT(*) FROM ownership_transfers WHERE current_owner_id = ? OR new_owner_id = ?');
    $stmt->execute([$owner['id'], $owner['id']]);
    $stats['transfers'] = (int)$stmt->fetchColumn();
}
$pageTitle = __('dashboard');
$isAdminArea = false;
include APP_ROOT . '/includes/header.php';
?>
<div class="page-header"><h1><?= __('welcome') ?>, <?= e(currentUser()['full_name']) ?></h1></div>
<div class="row g-3 mb-4">
<div class="col-md-3"><div class="card stat-card p-3"><div class="text-muted small"><?= __('my_properties') ?></div><h3><?= $stats['properties'] ?></h3></div></div>
<div class="col-md-3"><div class="card stat-card gold p-3"><div class="text-muted small"><?= __('my_certificates') ?></div><h3><?= $stats['certificates'] ?></h3></div></div>
<div class="col-md-3"><div class="card stat-card warning p-3"><div class="text-muted small"><?= __('transfers') ?></div><h3><?= $stats['transfers'] ?></h3></div></div>
<div class="col-md-3"><div class="card stat-card danger p-3"><div class="text-muted small"><?= __('notifications') ?></div><h3><?= $stats['notifications'] ?></h3></div></div>
</div>
<div class="row g-3">
<div class="col-md-6"><div class="card"><div class="card-body"><h5><?= __('my_properties') ?></h5><p class="text-muted">View and manage your registered properties.</p><a href="properties.php" class="btn btn-primary btn-sm"><?= __('view') ?></a></div></div></div>
<div class="col-md-6"><div class="card"><div class="card-body"><h5><?= __('request_transfer') ?></h5><p class="text-muted">Submit ownership transfer requests.</p><a href="transfers.php" class="btn btn-warning btn-sm"><?= __('request_transfer') ?></a></div></div></div>
</div>
<?php include APP_ROOT . '/includes/footer.php'; ?>
