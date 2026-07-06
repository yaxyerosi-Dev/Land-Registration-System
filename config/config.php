<?php
declare(strict_types=1);

define('APP_NAME', 'LandReg Pro');
define('APP_ROOT', dirname(__DIR__));
define('BASE_URL', '/land_regis');
define('UPLOAD_PATH', APP_ROOT . '/assets/uploads/');
define('UPLOAD_URL', BASE_URL . '/assets/uploads/');

define('PRIMARY_COLOR', '#006D77');
define('SECONDARY_COLOR', '#0B2545');
define('ACCENT_GOLD', '#D4A017');

session_start();

require_once APP_ROOT . '/config/database.php';
require_once APP_ROOT . '/includes/functions.php';
require_once APP_ROOT . '/includes/lang.php';
require_once APP_ROOT . '/includes/csrf.php';
require_once APP_ROOT . '/includes/auth.php';
