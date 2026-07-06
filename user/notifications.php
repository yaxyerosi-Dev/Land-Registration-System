<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/config/config.php';
requireLogin();
$db = getDB();
$userId = (int)currentUser()['id'];

if (isset($_GET['read'])) {
    $db->prepare('UPDATE notifications SET is_read=1 WHERE id=? AND user_id=?')->execute([(int)$_GET['read'], $userId]);
    redirect(BASE_URL . '/' . (isAdmin() ? 'admin' : 'user') . '/notifications.php');
}

$notifications = $db->prepare('SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT 50');
$notifications->execute([$userId]);
$items = $notifications->fetchAll();
$pageTitle = __('notifications');
$isAdminArea = isAdmin();
include APP_ROOT . '/includes/header.php';
?>
<div class="page-header"><h1><?= __('notifications') ?></h1></div>
<div class="card"><div class="list-group list-group-flush">
<?php foreach ($items as $n): ?>
<div class="list-group-item <?= $n['is_read'] ? '' : 'bg-light' ?>">
<div class="d-flex justify-content-between"><strong><?= e($n['title']) ?></strong><small class="text-muted"><?= e($n['created_at']) ?></small></div>
<p class="mb-1"><?= e($n['message']) ?></p>
<?php if (!$n['is_read']): ?><a href="?read=<?= $n['id'] ?>" class="btn btn-sm btn-outline-primary"><?= __('mark_read') ?></a><?php endif; ?>
</div>
<?php endforeach; ?>
<?php if(empty($items)): ?><div class="list-group-item text-center text-muted py-4"><?= __('no_records') ?></div><?php endif; ?>
</div></div>
<?php include APP_ROOT . '/includes/footer.php'; ?>
