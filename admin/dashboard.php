<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/config/config.php';
requireAdmin();

$db = getDB();
$stats = [
    'lands' => (int) $db->query('SELECT COUNT(*) FROM lands')->fetchColumn(),
    'owners' => (int) $db->query('SELECT COUNT(*) FROM owners')->fetchColumn(),
    'properties' => (int) $db->query('SELECT COUNT(*) FROM properties')->fetchColumn(),
    'transfers' => (int) $db->query('SELECT COUNT(*) FROM ownership_transfers')->fetchColumn(),
    'certificates' => (int) $db->query('SELECT COUNT(*) FROM certificates')->fetchColumn(),
    'pending' => (int) $db->query('SELECT COUNT(*) FROM ownership_transfers WHERE status = "Pending"')->fetchColumn(),
];

$activities = $db->query('SELECT a.*, u.full_name FROM audit_logs a LEFT JOIN users u ON a.user_id = u.id ORDER BY a.created_at DESC LIMIT 10')->fetchAll();

$pageTitle = __('dashboard');
$isAdminArea = true;
include APP_ROOT . '/includes/header.php';
?>

<div class="page-header">
    <h1><?= __('welcome') ?>, <?= e(currentUser()['full_name']) ?></h1>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-4 col-lg-2">
        <div class="card stat-card p-3">
            <div class="d-flex justify-content-between align-items-center">
                <div><div class="text-muted small"><?= __('total_lands') ?></div><h3 class="mb-0"><?= $stats['lands'] ?></h3></div>
                <div class="stat-icon bg-primary-soft"><i class="bi bi-map"></i></div>
            </div>
        </div>
    </div>
    <div class="col-md-4 col-lg-2">
        <div class="card stat-card gold p-3">
            <div class="d-flex justify-content-between align-items-center">
                <div><div class="text-muted small"><?= __('total_owners') ?></div><h3 class="mb-0"><?= $stats['owners'] ?></h3></div>
                <div class="stat-icon bg-gold-soft"><i class="bi bi-person-badge"></i></div>
            </div>
        </div>
    </div>
    <div class="col-md-4 col-lg-2">
        <div class="card stat-card success p-3">
            <div class="d-flex justify-content-between align-items-center">
                <div><div class="text-muted small"><?= __('total_properties') ?></div><h3 class="mb-0"><?= $stats['properties'] ?></h3></div>
                <div class="stat-icon bg-success-soft"><i class="bi bi-house-door"></i></div>
            </div>
        </div>
    </div>
    <div class="col-md-4 col-lg-2">
        <div class="card stat-card warning p-3">
            <div class="d-flex justify-content-between align-items-center">
                <div><div class="text-muted small"><?= __('total_transfers') ?></div><h3 class="mb-0"><?= $stats['transfers'] ?></h3></div>
                <div class="stat-icon bg-warning-soft"><i class="bi bi-arrow-left-right"></i></div>
            </div>
        </div>
    </div>
    <div class="col-md-4 col-lg-2">
        <div class="card stat-card p-3">
            <div class="d-flex justify-content-between align-items-center">
                <div><div class="text-muted small"><?= __('total_certificates') ?></div><h3 class="mb-0"><?= $stats['certificates'] ?></h3></div>
                <div class="stat-icon bg-primary-soft"><i class="bi bi-award"></i></div>
            </div>
        </div>
    </div>
    <div class="col-md-4 col-lg-2">
        <div class="card stat-card danger p-3">
            <div class="d-flex justify-content-between align-items-center">
                <div><div class="text-muted small"><?= __('pending_transfers') ?></div><h3 class="mb-0"><?= $stats['pending'] ?></h3></div>
                <div class="stat-icon bg-danger-soft"><i class="bi bi-clock"></i></div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header py-3"><?= __('recent_activities') ?></div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead><tr><th>User</th><th>Action</th><th>Description</th><th>Date</th></tr></thead>
                <tbody>
                <?php foreach ($activities as $a): ?>
                <tr>
                    <td><?= e($a['full_name'] ?? 'System') ?></td>
                    <td><?= e($a['action']) ?></td>
                    <td><?= e($a['description'] ?? '') ?></td>
                    <td><?= e(date('M d, Y H:i', strtotime($a['created_at']))) ?></td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($activities)): ?><tr><td colspan="4" class="text-center text-muted"><?= __('no_records') ?></td></tr><?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include APP_ROOT . '/includes/footer.php'; ?>
