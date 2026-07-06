<?php
declare(strict_types=1);
require_once dirname(__DIR__, 2) . '/config/config.php';
requireAdmin();
$db = getDB();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    requireCsrf();
    $title = trim($_POST['title'] ?? '');
    $message = trim($_POST['message'] ?? '');
    $type = $_POST['type'] ?? 'info';
    $target = $_POST['target'] ?? 'all';
    $userId = !empty($_POST['user_id']) ? (int)$_POST['user_id'] : null;

    if ($title && $message) {
        if ($target === 'all') {
            notifyAllUsers($title, $message, $type);
        } else {
            notifyUser($userId, $title, $message, $type);
        }
        logActivity('Notification Sent', $title);
        flash('success', __('saved_success'));
    }
    redirect(BASE_URL . '/admin/notifications/index.php');
}

$users = $db->query("SELECT id, full_name, email FROM users WHERE status='active' ORDER BY full_name")->fetchAll();
$pageTitle = __('send_notification');
$isAdminArea = true;
include APP_ROOT . '/includes/header.php';
?>
<div class="page-header"><h1><?= __('send_notification') ?></h1></div>
<div class="card"><div class="card-body"><form method="POST"><?= csrfField() ?>
<div class="mb-3"><label class="form-label"><?= __('title') ?></label><input name="title" class="form-control" required></div>
<div class="mb-3"><label class="form-label"><?= __('message') ?></label><textarea name="message" class="form-control" rows="4" required></textarea></div>
<div class="row g-3">
<div class="col-md-4"><label class="form-label">Type</label><select name="type" class="form-select"><option value="info">Info</option><option value="success">Success</option><option value="warning">Warning</option><option value="danger">Danger</option></select></div>
<div class="col-md-4"><label class="form-label">Target</label><select name="target" class="form-select" id="targetSelect"><option value="all"><?= __('all') ?> Users</option><option value="single">Single User</option></select></div>
<div class="col-md-4" id="userSelectWrap" style="display:none"><label class="form-label">User</label><select name="user_id" class="form-select"><?php foreach($users as $u): ?><option value="<?= $u['id'] ?>"><?= e($u['full_name']) ?></option><?php endforeach; ?></select></div>
</div>
<div class="mt-3"><button class="btn btn-primary"><?= __('send_notification') ?></button></div>
</form></div></div>
<script>document.getElementById('targetSelect').addEventListener('change',function(){document.getElementById('userSelectWrap').style.display=this.value==='single'?'block':'none';});</script>
<?php include APP_ROOT . '/includes/footer.php'; ?>
