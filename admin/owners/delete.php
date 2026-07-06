<?php
declare(strict_types=1);
require_once dirname(__DIR__, 2) . '/config/config.php';
requireAdmin();

$id = (int)($_GET['id'] ?? 0);
$db = getDB();
$stmt = $db->prepare('SELECT full_name FROM owners WHERE id = ?');
$stmt->execute([$id]);
$owner = $stmt->fetch();
if ($owner) {
    $db->prepare('DELETE FROM owners WHERE id = ?')->execute([$id]);
    logActivity('Owner Deleted', $owner['full_name']);
    flash('success', __('deleted_success'));
}
redirect(BASE_URL . '/admin/owners/index.php');
