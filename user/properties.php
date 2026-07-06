<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/config/config.php';
requireUser();
$db = getDB();
$owner = getOwnerByUserId((int)currentUser()['id']);
$properties = [];
if ($owner) {
    $stmt = $db->prepare('SELECT p.*, l.plot_number, l.region, l.district FROM properties p JOIN lands l ON p.land_id=l.id WHERE p.owner_id = ? ORDER BY p.id DESC');
    $stmt->execute([$owner['id']]);
    $properties = $stmt->fetchAll();
}
$pageTitle = __('my_properties');
$isAdminArea = false;
include APP_ROOT . '/includes/header.php';
?>
<div class="page-header"><h1><?= __('my_properties') ?></h1></div>
<div class="card"><div class="table-responsive"><table class="table table-hover mb-0">
<thead><tr><th><?= __('property_number') ?></th><th><?= __('plot_number') ?></th><th>Location</th><th><?= __('status') ?></th><th><?= __('actions') ?></th></tr></thead>
<tbody>
<?php foreach ($properties as $p): ?>
<tr>
<td><?= e($p['property_number']) ?></td><td><?= e($p['plot_number']) ?></td><td><?= e($p['region'].' / '.$p['district']) ?></td>
<td><span class="badge bg-success"><?= e($p['status']) ?></span></td>
<td><a href="certificates.php?property=<?= $p['id'] ?>" class="btn btn-sm btn-outline-primary"><?= __('certificates') ?></a></td>
</tr>
<?php endforeach; ?>
<?php if(empty($properties)): ?><tr><td colspan="5" class="text-center text-muted py-4"><?= __('no_records') ?></td></tr><?php endif; ?>
</tbody></table></div></div>
<?php include APP_ROOT . '/includes/footer.php'; ?>
