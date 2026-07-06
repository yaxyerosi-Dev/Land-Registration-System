<?php
declare(strict_types=1);
require_once dirname(__DIR__, 2) . '/config/config.php';
requireAdmin();
$id = (int)($_GET['id'] ?? 0);
$db = getDB();
$stmt = $db->prepare('SELECT c.*, p.property_number, p.registration_number, o.full_name AS owner_name, l.plot_number, l.region, l.district, l.neighborhood, l.land_size
    FROM certificates c JOIN properties p ON c.property_id=p.id JOIN owners o ON p.owner_id=o.id JOIN lands l ON p.land_id=l.id WHERE c.id=?');
$stmt->execute([$id]);
$cert = $stmt->fetch();
if (!$cert) redirect(BASE_URL . '/admin/certificates/index.php');
$pageTitle = __('view') . ' - ' . __('certificates');
$isAdminArea = true;
include APP_ROOT . '/includes/header.php';
?>
<div class="page-header"><h1><?= __('ownership_certificate') ?></h1>
<a href="download.php?cert=<?= urlencode($cert['certificate_number']) ?>" class="btn btn-success"><?= __('download') ?> PDF</a></div>
<div class="certificate-preview">
<h2 class="text-center" style="color:#006D77"><?= e(getSettings()['system_name']) ?></h2>
<h4 class="text-center text-muted"><?= __('ownership_certificate') ?></h4>
<hr style="border-color:#D4A017">
<div class="row">
<div class="col-md-8">
<p><strong><?= __('certificate_number') ?>:</strong> <?= e($cert['certificate_number']) ?></p>
<p><strong><?= __('owners') ?>:</strong> <?= e($cert['owner_name']) ?></p>
<p><strong><?= __('property_number') ?>:</strong> <?= e($cert['property_number']) ?></p>
<p><strong><?= __('registration_number') ?>:</strong> <?= e($cert['registration_number']) ?></p>
<p><strong><?= __('plot_number') ?>:</strong> <?= e($cert['plot_number']) ?></p>
<p><strong><?= __('region') ?>:</strong> <?= e($cert['region']) ?> / <?= e($cert['district']) ?> / <?= e($cert['neighborhood']) ?></p>
<p><strong><?= __('land_size') ?>:</strong> <?= e($cert['land_size']) ?></p>
<p><strong><?= __('issue_date') ?>:</strong> <?= e($cert['issue_date']) ?></p>
</div>
<div class="col-md-4 text-center">
<?php if ($cert['qr_code']): ?><img src="<?= UPLOAD_URL . e($cert['qr_code']) ?>" alt="QR" width="150"><p class="small">Scan to verify</p><?php endif; ?>
</div>
</div>
</div>
<?php include APP_ROOT . '/includes/footer.php'; ?>
