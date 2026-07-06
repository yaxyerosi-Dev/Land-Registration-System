<?php
declare(strict_types=1);
require_once dirname(__DIR__, 2) . '/config/config.php';
requireAdmin();

$db = getDB();
$page = max(1, (int)($_GET['page'] ?? 1));
$search = trim($_GET['search'] ?? '');

$where = '';
$params = [];
if ($search) {
    $where = ' WHERE full_name LIKE ? OR national_id LIKE ? OR email LIKE ?';
    $params = ["%$search%", "%$search%", "%$search%"];
}

$countStmt = $db->prepare('SELECT COUNT(*) FROM owners' . $where);
$countStmt->execute($params);
$pagination = paginate((int)$countStmt->fetchColumn(), $page);

$sql = 'SELECT o.*, u.email AS user_email FROM owners o LEFT JOIN users u ON o.user_id = u.id' . $where . ' ORDER BY o.id DESC LIMIT ' . $pagination['per_page'] . ' OFFSET ' . $pagination['offset'];
$stmt = $db->prepare($sql);
$stmt->execute($params);
$owners = $stmt->fetchAll();

$pageTitle = __('owners');
$isAdminArea = true;
include APP_ROOT . '/includes/header.php';
?>

<div class="page-header">
    <h1><?= __('owners') ?></h1>
    <a href="create.php" class="btn btn-primary"><i class="bi bi-plus-lg"></i> <?= __('add') ?></a>
</div>

<div class="card mb-3">
    <div class="card-body">
        <form method="GET" class="row g-2">
            <div class="col-md-10"><input type="text" name="search" class="form-control" placeholder="<?= __('search') ?>..." value="<?= e($search) ?>"></div>
            <div class="col-md-2"><button class="btn btn-outline-primary w-100"><?= __('search') ?></button></div>
        </form>
    </div>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead><tr>
                <th>ID</th><th><?= __('full_name') ?></th><th><?= __('national_id') ?></th><th><?= __('phone') ?></th><th><?= __('email') ?></th><th><?= __('actions') ?></th>
            </tr></thead>
            <tbody>
            <?php foreach ($owners as $o): ?>
            <tr>
                <td><?= $o['id'] ?></td>
                <td><?= e($o['full_name']) ?></td>
                <td><?= e($o['national_id']) ?></td>
                <td><?= e($o['phone']) ?></td>
                <td><?= e($o['email']) ?></td>
                <td>
                    <a href="edit.php?id=<?= $o['id'] ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                    <a href="delete.php?id=<?= $o['id'] ?>" class="btn btn-sm btn-outline-danger" data-confirm="<?= __('confirm_delete') ?>"><i class="bi bi-trash"></i></a>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($owners)): ?><tr><td colspan="6" class="text-center text-muted py-4"><?= __('no_records') ?></td></tr><?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?= renderPagination($pagination['total_pages'], $pagination['page'], 'index.php?search=' . urlencode($search)) ?>

<?php include APP_ROOT . '/includes/footer.php'; ?>
