<?php
declare(strict_types=1);
require_once dirname(__DIR__, 2) . '/config/config.php';
requireAdmin();
$db = getDB();
$page = max(1, (int)($_GET['page'] ?? 1));
$status = $_GET['status'] ?? '';
$where = ''; $params = [];
if ($status) { $where = ' WHERE t.status = ?'; $params = [$status]; }
$countStmt = $db->prepare('SELECT COUNT(*) FROM ownership_transfers t' . $where);
$countStmt->execute($params);
$pagination = paginate((int)$countStmt->fetchColumn(), $page);
$sql = 'SELECT t.*, p.property_number, co.full_name AS current_owner, no.full_name AS new_owner
        FROM ownership_transfers t
        JOIN properties p ON t.property_id = p.id
        JOIN owners co ON t.current_owner_id = co.id
        JOIN owners no ON t.new_owner_id = no.id' . $where . ' ORDER BY t.id DESC LIMIT ' . $pagination['per_page'] . ' OFFSET ' . $pagination['offset'];
$stmt = $db->prepare($sql);
$stmt->execute($params);
$transfers = $stmt->fetchAll();
$pageTitle = __('transfers');
$isAdminArea = true;
include APP_ROOT . '/includes/header.php';
?>
<div class="page-header"><h1><?= __('transfers') ?></h1></div>
<div class="card mb-3"><div class="card-body">
<form method="GET" class="row g-2"><div class="col-md-4"><select name="status" class="form-select"><option value=""><?= __('all') ?></option><option <?= $status==='Pending'?'selected':'' ?>>Pending</option><option <?= $status==='Approved'?'selected':'' ?>>Approved</option><option <?= $status==='Rejected'?'selected':'' ?>>Rejected</option></select></div><div class="col-md-2"><button class="btn btn-outline-primary"><?= __('filter') ?></button></div></form>
</div></div>
<div class="card"><div class="table-responsive"><table class="table table-hover mb-0">
<thead><tr><th>Property</th><th><?= __('current_owner') ?></th><th><?= __('new_owner') ?></th><th><?= __('transfer_reason') ?></th><th><?= __('status') ?></th><th><?= __('actions') ?></th></tr></thead>
<tbody>
<?php foreach ($transfers as $t): ?>
<tr>
<td><?= e($t['property_number']) ?></td><td><?= e($t['current_owner']) ?></td><td><?= e($t['new_owner']) ?></td><td><?= e(substr($t['transfer_reason']??'',0,50)) ?></td>
<td><span class="badge bg-<?= $t['status']==='Pending'?'warning':($t['status']==='Approved'?'success':'danger') ?>"><?= e($t['status']) ?></span></td>
<td>
<?php if ($t['status'] === 'Pending'): ?>
<a href="approve.php?id=<?= $t['id'] ?>" class="btn btn-sm btn-success"><?= __('approve') ?></a>
<a href="reject.php?id=<?= $t['id'] ?>" class="btn btn-sm btn-danger"><?= __('reject') ?></a>
<?php else: ?>—<?php endif; ?>
</td>
</tr>
<?php endforeach; ?>
<?php if(empty($transfers)): ?><tr><td colspan="6" class="text-center text-muted py-4"><?= __('no_records') ?></td></tr><?php endif; ?>
</tbody></table></div></div>
<?= renderPagination($pagination['total_pages'], $pagination['page'], 'index.php?status=' . urlencode($status)) ?>
<?php include APP_ROOT . '/includes/footer.php'; ?>
