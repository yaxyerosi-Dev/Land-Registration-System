<?php
declare(strict_types=1);
require_once dirname(__DIR__, 2) . '/config/config.php';
requireAdmin();
$id = (int)($_GET['id'] ?? 0);
$db = getDB();
$stmt = $db->prepare('SELECT plot_number FROM lands WHERE id = ?');
$stmt->execute([$id]);
if ($row = $stmt->fetch()) {
    $db->prepare('DELETE FROM lands WHERE id = ?')->execute([$id]);
    logActivity('Land Deleted', $row['plot_number']);
    flash('success', __('deleted_success'));
}
redirect(BASE_URL . '/admin/lands/index.php');
