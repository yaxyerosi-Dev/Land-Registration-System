<?php
declare(strict_types=1);
require_once dirname(__DIR__, 2) . '/config/config.php';
requireAdmin();
$id = (int)($_GET['id'] ?? 0);
$db = getDB();
$db->prepare('DELETE FROM properties WHERE id = ?')->execute([$id]);
logActivity('Property Deleted', "Property ID $id");
flash('success', __('deleted_success'));
redirect(BASE_URL . '/admin/properties/index.php');
