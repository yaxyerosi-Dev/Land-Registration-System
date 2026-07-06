<?php
declare(strict_types=1);
require_once dirname(__DIR__, 2) . '/config/config.php';
requireAdmin();
$db = getDB();
$id = (int)($_GET['id'] ?? 0);
$sql = 'SELECT p.*, o.full_name AS owner_name, o.national_id, l.plot_number, l.land_number, l.region, l.district, l.neighborhood, l.land_size, l.land_type,
        c.certificate_number, c.issue_date, c.qr_code
        FROM properties p
        JOIN owners o ON p.owner_id = o.id
        JOIN lands l ON p.land_id = l.id
        LEFT JOIN certificates c ON c.property_id = p.id AND c.status = "Valid"
        WHERE p.id = ?';
$stmt = $db->prepare($sql);
$stmt->execute([$id]);
$p = $stmt->fetch();
if (!$p) redirect(BASE_URL . '/admin/properties/index.php');
$pageTitle = __('view') . ' - ' . __('properties');
$isAdminArea = true;
include APP_ROOT . '/includes/header.php';
?>
<div class="page-header"><h1><?= __('properties') ?> #<?= e($p['property_number']) ?></h1>
<a href="edit.php?id=<?= $id ?>" class="btn btn-primary"><?= __('edit') ?></a>
<?php if ($p['certificate_number']): ?><a href="<?= BASE_URL ?>/admin/certificates/download.php?id=<?= $id ?>" class="btn btn-success"><?= __('download') ?> PDF</a><?php endif; ?>
</div>
<div class="row g-3">
<div class="col-md-6"><div class="card"><div class="card-header">Property Details</div><div class="card-body">
<p><strong><?= __('property_number') ?>:</strong> <?= e($p['property_number']) ?></p>
<p><strong><?= __('registration_number') ?>:</strong> <?= e($p['registration_number']) ?></p>
<p><strong><?= __('ownership_date') ?>:</strong> <?= e($p['ownership_date']) ?></p>
<p><strong><?= __('status') ?>:</strong> <?= e($p['status']) ?></p>
</div></div></div>
<div class="col-md-6"><div class="card"><div class="card-header"><?= __('owners') ?></div><div class="card-body">
<p><strong><?= __('full_name') ?>:</strong> <?= e($p['owner_name']) ?></p>
<p><strong><?= __('national_id') ?>:</strong> <?= e($p['national_id']) ?></p>
</div></div></div>
<div class="col-md-6"><div class="card"><div class="card-header"><?= __('lands') ?></div><div class="card-body">
<p><strong><?= __('plot_number') ?>:</strong> <?= e($p['plot_number']) ?></p>
<p><strong><?= __('region') ?>:</strong> <?= e($p['region']) ?> / <?= e($p['district']) ?> / <?= e($p['neighborhood']) ?></p>
<p><strong><?= __('land_size') ?>:</strong> <?= e($p['land_size']) ?></p>
</div></div></div>
<?php if ($p['certificate_number']): ?>
<div class="col-md-6"><div class="card"><div class="card-header"><?= __('certificates') ?></div><div class="card-body">
<p><strong><?= __('certificate_number') ?>:</strong> <?= e($p['certificate_number']) ?></p>
<p><strong><?= __('issue_date') ?>:</strong> <?= e($p['issue_date']) ?></p>
<?php if ($p['qr_code']): ?><img src="<?= UPLOAD_URL . e($p['qr_code']) ?>" alt="QR" width="120"><?php endif; ?>
</div></div></div>
<?php endif; ?>
</div>
<?php include APP_ROOT . '/includes/footer.php'; ?>
