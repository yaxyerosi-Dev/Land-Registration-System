<?php
declare(strict_types=1);
// require_once dirname(__DIR__, 2) . '/config/config.php';
require_once dirname(__DIR__) . '/config/config.php';
requireAdmin();
$db = getDB();
$page = max(1, (int)($_GET['page'] ?? 1));
$pagination = paginate((int)$db->query('SELECT COUNT(*) FROM audit_logs')->fetchColumn(), $page);
$logs = $db->query('SELECT a.*, u.full_name FROM audit_logs a LEFT JOIN users u ON a.user_id=u.id ORDER BY a.id DESC LIMIT ' . $pagination['per_page'] . ' OFFSET ' . $pagination['offset'])->fetchAll();
$pageTitle = __('audit_logs');
$isAdminArea = true;
include APP_ROOT . '/includes/header.php';
?>
<div class="page-header"><h1><?= __('audit_logs') ?></h1></div>
<div class="card"><div class="table-responsive"><table class="table table-hover mb-0">
<thead><tr><th>ID</th><th>User</th><th>Action</th><th>Description</th><th>Date</th></tr></thead>
<tbody>
<?php foreach ($logs as $l): ?>
<tr><td><?= $l['id'] ?></td><td><?= e($l['full_name']??'System') ?></td><td><?= e($l['action']) ?></td><td><?= e($l['description']??'') ?></td><td><?= e($l['created_at']) ?></td></tr>
<?php endforeach; ?>
</tbody></table></div></div>
<?= renderPagination($pagination['total_pages'], $pagination['page'], 'audit_logs.php') ?>
<?php include APP_ROOT . '/includes/footer.php'; ?>
