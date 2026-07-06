<?php
declare(strict_types=1);
require_once dirname(__DIR__, 2) . '/config/config.php';
requireAdmin();
$db = getDB();
$page = max(1, (int)($_GET['page'] ?? 1));
$search = trim($_GET['search'] ?? '');
$where = ''; $params = [];
if ($search) { $where = ' WHERE c.certificate_number LIKE ? OR p.property_number LIKE ?'; $params = ["%$search%", "%$search%"]; }
$countStmt = $db->prepare('SELECT COUNT(*) FROM certificates c JOIN properties p ON c.property_id=p.id' . $where);
$countStmt->execute($params);
$pagination = paginate((int)$countStmt->fetchColumn(), $page);
$sql = 'SELECT c.*, p.property_number, o.full_name AS owner_name FROM certificates c JOIN properties p ON c.property_id=p.id JOIN owners o ON p.owner_id=o.id' . $where . ' ORDER BY c.id DESC LIMIT ' . $pagination['per_page'] . ' OFFSET ' . $pagination['offset'];
$stmt = $db->prepare($sql);
$stmt->execute($params);
$certs = $stmt->fetchAll();
$pageTitle = __('certificates');
$isAdminArea = true;
include APP_ROOT . '/includes/header.php';
?>
<div class="page-header"><h1><?= __('certificates') ?></h1></div>
<div class="card mb-3"><div class="card-body"><form method="GET" class="row g-2"><div class="col-md-10"><input name="search" class="form-control" value="<?= e($search) ?>"></div><div class="col-md-2"><button class="btn btn-outline-primary w-100"><?= __('search') ?></button></div></form></div></div>
<div class="card"><div class="table-responsive"><table class="table table-hover mb-0">
<thead><tr><th><?= __('certificate_number') ?></th><th><?= __('property_number') ?></th><th><?= __('owners') ?></th><th><?= __('issue_date') ?></th><th><?= __('status') ?></th><th><?= __('actions') ?></th></tr></thead>
<tbody>
<?php foreach ($certs as $c): ?>
<tr>
<td><?= e($c['certificate_number']) ?></td><td><?= e($c['property_number']) ?></td><td><?= e($c['owner_name']) ?></td><td><?= e($c['issue_date']) ?></td>
<td><span class="badge bg-<?= $c['status']==='Valid'?'success':'secondary' ?>"><?= e($c['status']) ?></span></td>
<td>
<a href="view.php?id=<?= $c['id'] ?>" class="btn btn-sm btn-outline-info"><i class="bi bi-eye"></i></a>
<a href="download.php?cert=<?= urlencode($c['certificate_number']) ?>" class="btn btn-sm btn-outline-success"><i class="bi bi-download"></i></a>
</td>
</tr>
<?php endforeach; ?>
<?php if(empty($certs)): ?><tr><td colspan="6" class="text-center text-muted py-4"><?= __('no_records') ?></td></tr><?php endif; ?>
</tbody></table></div></div>
<?= renderPagination($pagination['total_pages'], $pagination['page'], 'index.php?search=' . urlencode($search)) ?>
<?php include APP_ROOT . '/includes/footer.php'; ?>
