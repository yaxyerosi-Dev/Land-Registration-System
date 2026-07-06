<?php
declare(strict_types=1);
require_once __DIR__ . '/config/config.php';

$certNumber = trim($_GET['cert'] ?? $_POST['cert'] ?? '');
$result = null;
if ($certNumber) {
    $result = getCertificateDetails($certNumber);
}

$pageTitle = __('verify_certificate');
$settings = getSettings();
?>
<!DOCTYPE html>
<html lang="<?= e(getLang()) ?>" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?> - <?= e($settings['system_name']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/assets/css/style.css" rel="stylesheet">
</head>
<body class="auth-page">
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="auth-card" style="max-width:700px">
                <div class="auth-logo"><i class="bi bi-shield-check"></i><h2><?= __('verify_certificate') ?></h2></div>
                <form method="GET" class="mb-4">
                    <div class="input-group">
                        <input type="text" name="cert" class="form-control" placeholder="<?= __('enter_cert_number') ?>" value="<?= e($certNumber) ?>" required>
                        <button class="btn btn-primary"><?= __('verify') ?></button>
                    </div>
                </form>
                <?php if ($certNumber): ?>
                    <?php if ($result && $result['status'] === 'Valid'): ?>
                    <div class="verify-result valid">
                        <i class="bi bi-check-circle-fill text-success fs-1"></i>
                        <h4 class="text-success mt-2"><?= __('certificate_valid') ?></h4>
                        <hr>
                        <p><strong><?= __('certificate_number') ?>:</strong> <?= e($result['certificate_number']) ?></p>
                        <p><strong><?= __('owners') ?>:</strong> <?= e($result['owner_name']) ?></p>
                        <p><strong><?= __('property_number') ?>:</strong> <?= e($result['property_number']) ?></p>
                        <p><strong><?= __('plot_number') ?>:</strong> <?= e($result['plot_number']) ?></p>
                        <p><strong><?= __('region') ?>:</strong> <?= e($result['region']) ?> / <?= e($result['district']) ?> / <?= e($result['neighborhood']) ?></p>
                        <p><strong><?= __('land_size') ?>:</strong> <?= e($result['land_size']) ?></p>
                        <p><strong><?= __('issue_date') ?>:</strong> <?= e($result['issue_date']) ?></p>
                    </div>
                    <?php else: ?>
                    <div class="verify-result invalid">
                        <i class="bi bi-x-circle-fill text-danger fs-1"></i>
                        <h4 class="text-danger mt-2"><?= __('certificate_invalid') ?></h4>
                    </div>
                    <?php endif; ?>
                <?php endif; ?>
                <p class="text-center mt-3"><a href="<?= BASE_URL ?>/index.php"><?= __('home') ?></a></p>
            </div>
        </div>
    </div>
</div>
<script src="<?= BASE_URL ?>/assets/js/app.js"></script>
</body>
</html>
