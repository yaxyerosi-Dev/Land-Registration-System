<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/config/config.php';
requireAdmin();
redirect(BASE_URL . '/admin/settings/index.php');
