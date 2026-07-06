<?php
declare(strict_types=1);
require_once __DIR__ . '/config/config.php';

$q = trim($_GET['q'] ?? '');
$results = [];
$db = getDB();

if ($q) {
    $like = "%$q%";
    $sql = 'SELECT p.property_number, p.registration_number, o.full_name AS owner_name, l.plot_number, l.district, l.neighborhood, c.certificate_number, p.status
            FROM properties p
            JOIN owners o ON p.owner_id = o.id
            JOIN lands l ON p.land_id = l.id
            LEFT JOIN certificates c ON c.property_id = p.id AND c.status = "Valid"
            WHERE p.property_number LIKE ? OR p.registration_number LIKE ? OR o.full_name LIKE ?
            OR l.plot_number LIKE ? OR l.district LIKE ? OR l.neighborhood LIKE ? OR c.certificate_number LIKE ?
            ORDER BY p.id DESC LIMIT 50';
    $stmt = $db->prepare($sql);
    $stmt->execute(array_fill(0, 7, $like));
    $results = $stmt->fetchAll();
}

$pageTitle = __('search');
$settings = getSettings();
requireLogin();
$isAdminArea = isAdmin();
include APP_ROOT . '/includes/header.php';
?>
<div class="page-header"><h1><?= __('search') ?></h1></div>
<div class="card mb-3"><div class="card-body">
<form method="GET" class="row g-2">
<div class="col-md-10"><input type="text" name="q" class="form-control" placeholder="<?= __('search_placeholder') ?>" value="<?= e($q) ?>"></div>
<div class="col-md-2"><button class="btn btn-primary w-100"><?= __('search') ?></button></div>
</form>
</div></div>
<?php if ($q): ?>
<div class="card"><div class="table-responsive"><table class="table table-hover mb-0">
<thead><tr><th><?= __('property_number') ?></th><th><?= __('owners') ?></th><th><?= __('plot_number') ?></th><th><?= __('district') ?></th><th><?= __('certificate_number') ?></th><th><?= __('status') ?></th></tr></thead>
<tbody>
<?php foreach ($results as $r): ?>
<tr>
<td><?= e($r['property_number']) ?></td><td><?= e($r['owner_name']) ?></td><td><?= e($r['plot_number']) ?></td>
<td><?= e($r['district']) ?></td><td><?= e($r['certificate_number'] ?? '—') ?></td><td><?= e($r['status']) ?></td>
</tr>
<?php endforeach; ?>
<?php if(empty($results)): ?><tr><td colspan="6" class="text-center text-muted py-4"><?= __('no_records') ?></td></tr><?php endif; ?>
</tbody></table></div></div>
<?php endif; ?>
<?php include APP_ROOT . '/includes/footer.php'; ?>
