<?php
declare(strict_types=1);
require_once dirname(__DIR__, 2) . '/config/config.php';
requireAdmin();

$db = getDB();
$page = max(1, (int)($_GET['page'] ?? 1));
$search = trim($_GET['search'] ?? '');
$where = ''; $params = [];
if ($search) {
    $where = ' WHERE plot_number LIKE ? OR land_number LIKE ? OR district LIKE ? OR neighborhood LIKE ?';
    $params = array_fill(0, 4, "%$search%");
}
$countStmt = $db->prepare('SELECT COUNT(*) FROM lands' . $where);
$countStmt->execute($params);
$pagination = paginate((int)$countStmt->fetchColumn(), $page);
$stmt = $db->prepare('SELECT * FROM lands' . $where . ' ORDER BY id DESC LIMIT ' . $pagination['per_page'] . ' OFFSET ' . $pagination['offset']);
$stmt->execute($params);
$lands = $stmt->fetchAll();

$pageTitle = __('lands');
$isAdminArea = true;
include APP_ROOT . '/includes/header.php';
?>
<div class="page-header"><h1><?= __('lands') ?></h1><a href="create.php" class="btn btn-primary"><i class="bi bi-plus-lg"></i> <?= __('add') ?></a></div>
<div class="card mb-3"><div class="card-body"><form method="GET" class="row g-2"><div class="col-md-10"><input type="text" name="search" class="form-control" value="<?= e($search) ?>"></div><div class="col-md-2"><button class="btn btn-outline-primary w-100"><?= __('search') ?></button></div></form></div></div>
<div class="card"><div class="table-responsive"><table class="table table-hover mb-0">
<thead><tr><th><?= __('plot_number') ?></th><th><?= __('land_number') ?></th><th><?= __('region') ?></th><th><?= __('district') ?></th><th><?= __('land_type') ?></th><th><?= __('status') ?></th><th><?= __('actions') ?></th></tr></thead>
<tbody>
<?php foreach ($lands as $l): ?>
<tr>
<td><?= e($l['plot_number']) ?></td><td><?= e($l['land_number']) ?></td><td><?= e($l['region']) ?></td><td><?= e($l['district']) ?></td><td><?= e($l['land_type']) ?></td>
<td><span class="badge bg-<?= $l['status']==='Active'?'success':'secondary' ?>"><?= e($l['status']) ?></span></td>
<td><a href="edit.php?id=<?= $l['id'] ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a> <a href="delete.php?id=<?= $l['id'] ?>" class="btn btn-sm btn-outline-danger" data-confirm="<?= __('confirm_delete') ?>"><i class="bi bi-trash"></i></a></td>
</tr>
<?php endforeach; ?>
<?php if(empty($lands)): ?><tr><td colspan="7" class="text-center text-muted py-4"><?= __('no_records') ?></td></tr><?php endif; ?>
</tbody></table></div></div>
<?= renderPagination($pagination['total_pages'], $pagination['page'], 'index.php?search=' . urlencode($search)) ?>
<?php include APP_ROOT . '/includes/footer.php'; ?>
