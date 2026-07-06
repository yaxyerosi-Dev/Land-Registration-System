<?php
declare(strict_types=1);
require_once __DIR__ . '/config/config.php';

if (isLoggedIn()) {
    redirect(isAdmin() ? BASE_URL . '/admin/dashboard.php' : BASE_URL . '/user/dashboard.php');
}

$settings = getSettings();
$lang = getLang();
$pageTitle = $settings['system_name'] ?? APP_NAME;
?>
<!DOCTYPE html>
<html lang="<?= e($lang) ?>" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="LandReg Pro - Digital Land Registration and Property Record Management System">
    <title><?= e($pageTitle) ?> - <?= __('landing_tagline') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/assets/css/style.css" rel="stylesheet">
</head>
<body class="landing-page">

<nav class="landing-nav navbar navbar-expand-lg fixed-top">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center gap-2" href="<?= BASE_URL ?>/index.php">
            <?php if (!empty($settings['logo'])): ?>
                <img src="<?= UPLOAD_URL . e($settings['logo']) ?>" alt="Logo" height="36">
            <?php else: ?>
                <i class="bi bi-building brand-icon"></i>
            <?php endif; ?>
            <span><?= e($settings['system_name']) ?></span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#landingNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="landingNav">
            <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-2">
                <li class="nav-item"><a class="nav-link" href="#features"><?= __('landing_features') ?></a></li>
                <li class="nav-item"><a class="nav-link" href="#how-it-works"><?= __('landing_how_it_works') ?></a></li>
                <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/verify.php"><?= __('verify') ?></a></li>
                <li class="nav-item">
                    <div class="btn-group btn-group-sm ms-lg-2">
                        <a href="?lang=en" class="btn btn-outline-light <?= $lang === 'en' ? 'active' : '' ?>">EN</a>
                        <a href="?lang=so" class="btn btn-outline-light <?= $lang === 'so' ? 'active' : '' ?>">SO</a>
                    </div>
                </li>
                <li class="nav-item ms-lg-2">
                    <button class="btn btn-sm btn-outline-light" id="themeToggle" title="<?= __('dark_mode') ?>">
                        <i class="bi bi-moon-fill"></i>
                    </button>
                </li>
                <li class="nav-item ms-lg-2">
                    <a href="<?= BASE_URL ?>/login.php" class="btn btn-outline-light btn-sm"><?= __('login') ?></a>
                </li>
                <li class="nav-item ms-lg-1">
                    <a href="<?= BASE_URL ?>/register.php" class="btn btn-gold btn-sm"><?= __('register') ?></a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<section class="landing-hero">
    <div class="hero-overlay"></div>
    <div class="container position-relative">
        <div class="row align-items-center min-vh-100 py-5">
            <div class="col-lg-7 hero-content">
                <span class="hero-badge"><i class="bi bi-shield-check"></i> <?= __('landing_gov_system') ?></span>
                <h1 class="hero-title"><?= e($settings['system_name']) ?></h1>
                <p class="hero-subtitle"><?= __('landing_tagline') ?></p>
                <p class="hero-desc"><?= __('landing_description') ?></p>
                <div class="hero-actions">
                    <a href="<?= BASE_URL ?>/login.php" class="btn btn-light btn-lg px-4">
                        <i class="bi bi-box-arrow-in-right"></i> <?= __('login') ?>
                    </a>
                    <a href="<?= BASE_URL ?>/register.php" class="btn btn-gold btn-lg px-4">
                        <i class="bi bi-person-plus"></i> <?= __('register') ?>
                    </a>
                    <a href="<?= BASE_URL ?>/verify.php" class="btn btn-outline-light btn-lg px-4">
                        <i class="bi bi-qr-code-scan"></i> <?= __('verify_certificate') ?>
                    </a>
                </div>
            </div>
            <div class="col-lg-5 d-none d-lg-block">
                <div class="hero-card">
                    <div class="hero-stat"><i class="bi bi-map"></i><div><strong><?= __('lands') ?></strong><span><?= __('landing_stat_lands') ?></span></div></div>
                    <div class="hero-stat"><i class="bi bi-house-door"></i><div><strong><?= __('properties') ?></strong><span><?= __('landing_stat_properties') ?></span></div></div>
                    <div class="hero-stat"><i class="bi bi-award"></i><div><strong><?= __('certificates') ?></strong><span><?= __('landing_stat_certificates') ?></span></div></div>
                    <div class="hero-stat"><i class="bi bi-arrow-left-right"></i><div><strong><?= __('transfers') ?></strong><span><?= __('landing_stat_transfers') ?></span></div></div>
                </div>
            </div>
        </div>
    </div>
</section>

<section id="features" class="landing-section">
    <div class="container">
        <div class="section-header text-center">
            <h2><?= __('landing_features') ?></h2>
            <p><?= __('landing_features_desc') ?></p>
        </div>
        <div class="row g-4">
            <div class="col-md-6 col-lg-4">
                <div class="feature-card">
                    <div class="feature-icon bg-primary-soft"><i class="bi bi-map"></i></div>
                    <h5><?= __('lands') ?></h5>
                    <p><?= __('landing_feature_lands') ?></p>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="feature-card">
                    <div class="feature-icon bg-gold-soft"><i class="bi bi-person-badge"></i></div>
                    <h5><?= __('owners') ?></h5>
                    <p><?= __('landing_feature_owners') ?></p>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="feature-card">
                    <div class="feature-icon bg-success-soft"><i class="bi bi-house-door"></i></div>
                    <h5><?= __('properties') ?></h5>
                    <p><?= __('landing_feature_properties') ?></p>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="feature-card">
                    <div class="feature-icon bg-warning-soft"><i class="bi bi-award"></i></div>
                    <h5><?= __('certificates') ?></h5>
                    <p><?= __('landing_feature_certificates') ?></p>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="feature-card">
                    <div class="feature-icon bg-danger-soft"><i class="bi bi-arrow-left-right"></i></div>
                    <h5><?= __('transfers') ?></h5>
                    <p><?= __('landing_feature_transfers') ?></p>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="feature-card">
                    <div class="feature-icon bg-primary-soft"><i class="bi bi-file-earmark-bar-graph"></i></div>
                    <h5><?= __('reports') ?></h5>
                    <p><?= __('landing_feature_reports') ?></p>
                </div>
            </div>
        </div>
    </div>
</section>

<section id="how-it-works" class="landing-section landing-section-alt">
    <div class="container">
        <div class="section-header text-center">
            <h2><?= __('landing_how_it_works') ?></h2>
            <p><?= __('landing_how_desc') ?></p>
        </div>
        <div class="row g-4 justify-content-center">
            <div class="col-md-6 col-lg-3">
                <div class="step-card">
                    <div class="step-number">1</div>
                    <h6><?= __('register') ?></h6>
                    <p><?= __('landing_step1') ?></p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="step-card">
                    <div class="step-number">2</div>
                    <h6><?= __('landing_step2_title') ?></h6>
                    <p><?= __('landing_step2') ?></p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="step-card">
                    <div class="step-number">3</div>
                    <h6><?= __('certificates') ?></h6>
                    <p><?= __('landing_step3') ?></p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="step-card">
                    <div class="step-number">4</div>
                    <h6><?= __('verify') ?></h6>
                    <p><?= __('landing_step4') ?></p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="landing-cta">
    <div class="container text-center">
        <h2><?= __('landing_cta_title') ?></h2>
        <p class="mb-4"><?= __('landing_cta_desc') ?></p>
        <a href="<?= BASE_URL ?>/register.php" class="btn btn-gold btn-lg px-5 me-2"><?= __('create_account') ?></a>
        <a href="<?= BASE_URL ?>/verify.php" class="btn btn-outline-light btn-lg px-5"><?= __('verify_certificate') ?></a>
    </div>
</section>

<footer class="landing-footer">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-4">
                <h5><i class="bi bi-building"></i> <?= e($settings['system_name']) ?></h5>
                <p class="text-muted-small"><?= __('landing_tagline') ?></p>
            </div>
            <div class="col-lg-4">
                <h6><?= __('office_info') ?></h6>
                <?php if ($settings['office_name']): ?><p class="mb-1"><?= e($settings['office_name']) ?></p><?php endif; ?>
                <?php if ($settings['office_address']): ?><p class="mb-1"><i class="bi bi-geo-alt"></i> <?= e($settings['office_address']) ?></p><?php endif; ?>
                <?php if ($settings['office_phone']): ?><p class="mb-1"><i class="bi bi-telephone"></i> <?= e($settings['office_phone']) ?></p><?php endif; ?>
                <?php if ($settings['office_email']): ?><p class="mb-0"><i class="bi bi-envelope"></i> <?= e($settings['office_email']) ?></p><?php endif; ?>
            </div>
            <div class="col-lg-4">
                <h6><?= __('landing_quick_links') ?></h6>
                <ul class="footer-links">
                    <li><a href="<?= BASE_URL ?>/login.php"><?= __('login') ?></a></li>
                    <li><a href="<?= BASE_URL ?>/register.php"><?= __('register') ?></a></li>
                    <li><a href="<?= BASE_URL ?>/verify.php"><?= __('verify_certificate') ?></a></li>
                </ul>
            </div>
        </div>
        <hr class="footer-divider">
        <p class="text-center mb-0 copyright">&copy; <?= date('Y') ?> <?= e($settings['system_name']) ?>. <?= __('landing_rights') ?></p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= BASE_URL ?>/assets/js/app.js"></script>
</body>
</html>