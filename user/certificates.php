<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/config/config.php';
requireUser();
$db = getDB();
$owner = getOwnerByUserId((int)currentUser()['id']);
$propertyFilter = (int)($_GET['property'] ?? 0);
$certs = [];
if ($owner) {
    $sql = 'SELECT c.*, p.property_number FROM certificates c JOIN properties p ON c.property_id=p.id WHERE p.owner_id = ? AND c.status="Valid"';
    $params = [$owner['id']];
    if ($propertyFilter) { $sql .= ' AND p.id = ?'; $params[] = $propertyFilter; }
    $sql .= ' ORDER BY c.id DESC';
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $certs = $stmt->fetchAll();
}
$pageTitle = __('my_certificates');
$isAdminArea = false;
include APP_ROOT . '/includes/header.php';
?>
<div class="page-header"><h1><?= __('my_certificates') ?></h1></div>
<div class="card"><div class="table-responsive"><table class="table table-hover mb-0">
<thead><tr><th><?= __('certificate_number') ?></th><th><?= __('property_number') ?></th><th><?= __('issue_date') ?></th><th><?= __('actions') ?></th></tr></thead>
<tbody>
<?php foreach ($certs as $c): ?>
<tr>
<td><?= e($c['certificate_number']) ?></td><td><?= e($c['property_number']) ?></td><td><?= e($c['issue_date']) ?></td>
<td>
<a href="<?= BASE_URL ?>/admin/certificates/download.php?cert=<?= urlencode($c['certificate_number']) ?>" class="btn btn-sm btn-success"><?= __('download') ?></a>
<a href="<?= BASE_URL ?>/verify.php?cert=<?= urlencode($c['certificate_number']) ?>" class="btn btn-sm btn-outline-info"><?= __('verify') ?></a>
</td>
</tr>
<?php endforeach; ?>
<?php if(empty($certs)): ?><tr><td colspan="4" class="text-center text-muted py-4"><?= __('no_records') ?></td></tr><?php endif; ?>
</tbody></table></div></div>
<?php include APP_ROOT . '/includes/footer.php'; ?>
