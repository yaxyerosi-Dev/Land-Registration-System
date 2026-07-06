<?php
declare(strict_types=1);
require_once dirname(__DIR__, 2) . '/config/config.php';
requireAdmin();
$db = getDB();
$page = max(1, (int)($_GET['page'] ?? 1));
$search = trim($_GET['search'] ?? '');
$where = ''; $params = [];
if ($search) {
    $where = ' WHERE p.property_number LIKE ? OR p.registration_number LIKE ? OR o.full_name LIKE ?';
    $params = ["%$search%", "%$search%", "%$search%"];
}
$countStmt = $db->prepare('SELECT COUNT(*) FROM properties p JOIN owners o ON p.owner_id=o.id' . $where);
$countStmt->execute($params);
$pagination = paginate((int)$countStmt->fetchColumn(), $page);
$sql = 'SELECT p.*, o.full_name AS owner_name, l.plot_number FROM properties p JOIN owners o ON p.owner_id=o.id JOIN lands l ON p.land_id=l.id' . $where . ' ORDER BY p.id DESC LIMIT ' . $pagination['per_page'] . ' OFFSET ' . $pagination['offset'];
$stmt = $db->prepare($sql);
$stmt->execute($params);
$properties = $stmt->fetchAll();
$pageTitle = __('properties');
$isAdminArea = true;
include APP_ROOT . '/includes/header.php';
?>
<div class="page-header"><h1><?= __('properties') ?></h1><a href="create.php" class="btn btn-primary"><i class="bi bi-plus-lg"></i> <?= __('add') ?></a></div>
<div class="card mb-3"><div class="card-body"><form method="GET" class="row g-2"><div class="col-md-10"><input name="search" class="form-control" value="<?= e($search) ?>"></div><div class="col-md-2"><button class="btn btn-outline-primary w-100"><?= __('search') ?></button></div></form></div></div>
<div class="card"><div class="table-responsive"><table class="table table-hover mb-0">
<thead><tr><th><?= __('property_number') ?></th><th><?= __('registration_number') ?></th><th><?= __('owners') ?></th><th><?= __('plot_number') ?></th><th><?= __('status') ?></th><th><?= __('actions') ?></th></tr></thead>
<tbody>
<?php foreach ($properties as $p): ?>
<tr>
<td><?= e($p['property_number']) ?></td><td><?= e($p['registration_number']) ?></td><td><?= e($p['owner_name']) ?></td><td><?= e($p['plot_number']) ?></td>
<td><span class="badge bg-success"><?= e($p['status']) ?></span></td>
<td><a href="view.php?id=<?= $p['id'] ?>" class="btn btn-sm btn-outline-info"><i class="bi bi-eye"></i></a> <a href="edit.php?id=<?= $p['id'] ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a> <a href="delete.php?id=<?= $p['id'] ?>" class="btn btn-sm btn-outline-danger" data-confirm="<?= __('confirm_delete') ?>"><i class="bi bi-trash"></i></a></td>
</tr>
<?php endforeach; ?>
<?php if(empty($properties)): ?><tr><td colspan="6" class="text-center text-muted py-4"><?= __('no_records') ?></td></tr><?php endif; ?>
</tbody></table></div></div>
<?= renderPagination($pagination['total_pages'], $pagination['page'], 'index.php?search=' . urlencode($search)) ?>
<?php include APP_ROOT . '/includes/footer.php'; ?>
