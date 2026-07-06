<?php
declare(strict_types=1);
require_once dirname(__DIR__, 2) . '/config/config.php';
requireAdmin();
$db = getDB();
$page = max(1, (int)($_GET['page'] ?? 1));
$pagination = paginate((int)$db->query('SELECT COUNT(*) FROM users')->fetchColumn(), $page);
$stmt = $db->query('SELECT * FROM users ORDER BY id DESC LIMIT ' . $pagination['per_page'] . ' OFFSET ' . $pagination['offset']);
$users = $stmt->fetchAll();
$pageTitle = __('users');
$isAdminArea = true;
include APP_ROOT . '/includes/header.php';
?>
<div class="page-header"><h1><?= __('users') ?></h1><a href="create.php" class="btn btn-primary"><i class="bi bi-plus-lg"></i> <?= __('add') ?></a></div>
<div class="card"><div class="table-responsive"><table class="table table-hover mb-0">
<thead><tr><th>ID</th><th><?= __('full_name') ?></th><th><?= __('email') ?></th><th>Role</th><th><?= __('status') ?></th><th><?= __('actions') ?></th></tr></thead>
<tbody>
<?php foreach ($users as $u): ?>
<tr>
<td><?= $u['id'] ?></td><td><?= e($u['full_name']) ?></td><td><?= e($u['email']) ?></td>
<td><span class="badge bg-<?= $u['role']==='admin'?'primary':'secondary' ?>"><?= e($u['role']) ?></span></td>
<td><span class="badge bg-<?= $u['status']==='active'?'success':'danger' ?>"><?= e($u['status']) ?></span></td>
<td><a href="edit.php?id=<?= $u['id'] ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a></td>
</tr>
<?php endforeach; ?>
</tbody></table></div></div>
<?= renderPagination($pagination['total_pages'], $pagination['page'], 'index.php') ?>
<?php include APP_ROOT . '/includes/footer.php'; ?>
