<?php
declare(strict_types=1);
/**
 * LandReg Pro Installation Script
 * Run once: http://localhost/land_regis/install.php
 * DELETE this file after installation!
 */

$step = $_GET['step'] ?? '1';
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $step === '2') {
    $host = $_POST['db_host'] ?? 'localhost';
    $name = $_POST['db_name'] ?? 'landreg_pro';
    $user = $_POST['db_user'] ?? 'root';
    $pass = $_POST['db_pass'] ?? '';
    $adminEmail = $_POST['admin_email'] ?? 'admin@landreg.com';
    $adminPass = $_POST['admin_password'] ?? 'admin123';

    try {
        $pdo = new PDO("mysql:host=$host;charset=utf8mb4", $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
        $sql = file_get_contents(__DIR__ . '/database/landreg_pro.sql');
        $pdo->exec($sql);

        $pdo->exec("USE `$name`");
        $hash = password_hash($adminPass, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare('UPDATE users SET email = ?, password = ? WHERE role = "admin" LIMIT 1');
        $stmt->execute([$adminEmail, $hash]);

        $configContent = "<?php\ndeclare(strict_types=1);\n\ndefine('DB_HOST', " . var_export($host, true) . ");\n";
        $configContent .= "define('DB_NAME', " . var_export($name, true) . ");\n";
        $configContent .= "define('DB_USER', " . var_export($user, true) . ");\n";
        $configContent .= "define('DB_PASS', " . var_export($pass, true) . ");\n";
        $configContent .= "define('DB_CHARSET', 'utf8mb4');\n\nfunction getDB(): PDO\n{\n    static \$pdo = null;\n    if (\$pdo === null) {\n        \$dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;\n        \$options = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, PDO::ATTR_EMULATE_PREPARES => false];\n        \$pdo = new PDO(\$dsn, DB_USER, DB_PASS, \$options);\n    }\n    return \$pdo;\n}\n";
        file_put_contents(__DIR__ . '/config/database.php', $configContent);

        foreach (['photos', 'logos', 'qr_codes'] as $dir) {
            $path = __DIR__ . '/assets/uploads/' . $dir;
            if (!is_dir($path)) mkdir($path, 0755, true);
        }

        $success = 'Installation complete! Login at index.php. Delete install.php for security.';
        $step = '3';
    } catch (Exception $e) {
        $error = 'Installation failed: ' . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html><head><meta charset="UTF-8"><title>LandReg Pro - Install</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"></head>
<body class="bg-light"><div class="container py-5"><div class="card mx-auto" style="max-width:600px">
<div class="card-header bg-primary text-white"><h4 class="mb-0">LandReg Pro Installation</h4></div>
<div class="card-body">
<?php if ($error): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>
<?php if ($success): ?><div class="alert alert-success"><?= htmlspecialchars($success) ?></div><a href="index.php" class="btn btn-primary">Go to Login</a>
<?php elseif ($step === '1'): ?>
<p>Welcome to LandReg Pro setup. Ensure XAMPP Apache and MySQL are running.</p>
<p>Run <code>composer install</code> in the project folder for PDF/Excel/QR features.</p>
<a href="?step=2" class="btn btn-primary">Continue</a>
<?php else: ?>
<form method="POST">
<h5>Database Configuration</h5>
<div class="mb-3"><label>DB Host</label><input name="db_host" class="form-control" value="localhost"></div>
<div class="mb-3"><label>DB Name</label><input name="db_name" class="form-control" value="landreg_pro"></div>
<div class="mb-3"><label>DB User</label><input name="db_user" class="form-control" value="root"></div>
<div class="mb-3"><label>DB Password</label><input name="db_pass" type="password" class="form-control"></div>
<hr><h5>Admin Account</h5>
<div class="mb-3"><label>Admin Email</label><input name="admin_email" class="form-control" value="admin@landreg.com"></div>
<div class="mb-3"><label>Admin Password</label><input name="admin_password" type="password" class="form-control" value="admin123"></div>
<button class="btn btn-success">Install Now</button>
</form>
<?php endif; ?>
</div></div></div></body></html>
