<?php
declare(strict_types=1);

function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

function redirect(string $url): never
{
    header('Location: ' . $url);
    exit;
}

function flash(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function getFlash(): ?array
{
    if (!empty($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

function logActivity(string $action, ?string $description = null): void
{
    $db = getDB();
    $userId = $_SESSION['user_id'] ?? null;
    $stmt = $db->prepare('INSERT INTO audit_logs (user_id, action, description) VALUES (?, ?, ?)');
    $stmt->execute([$userId, $action, $description]);
}

function getSettings(): array
{
    static $settings = null;
    if ($settings === null) {
        $db = getDB();
        $stmt = $db->query('SELECT * FROM settings ORDER BY id ASC LIMIT 1');
        $settings = $stmt->fetch() ?: [
            'system_name' => APP_NAME,
            'office_name' => '',
            'office_address' => '',
            'office_phone' => '',
            'office_email' => '',
            'logo' => '',
        ];
    }
    return $settings;
}

function uploadFile(array $file, string $subdir, array $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp']): ?string
{
    if ($file['error'] !== UPLOAD_ERR_OK || empty($file['tmp_name'])) {
        return null;
    }
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed, true)) {
        return null;
    }
    $dir = UPLOAD_PATH . $subdir . '/';
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    $filename = uniqid('file_', true) . '.' . $ext;
    if (move_uploaded_file($file['tmp_name'], $dir . $filename)) {
        return $subdir . '/' . $filename;
    }
    return null;
}

function generateUniqueNumber(string $prefix): string
{
    return strtoupper($prefix) . '-' . date('Y') . '-' . strtoupper(substr(uniqid(), -6));
}

function paginate(int $total, int $page, int $perPage = 10): array
{
    $totalPages = max(1, (int) ceil($total / $perPage));
    $page = max(1, min($page, $totalPages));
    $offset = ($page - 1) * $perPage;
    return [
        'total' => $total,
        'page' => $page,
        'per_page' => $perPage,
        'total_pages' => $totalPages,
        'offset' => $offset,
    ];
}

function renderPagination(int $totalPages, int $currentPage, string $baseUrl): string
{
    if ($totalPages <= 1) {
        return '';
    }
    $html = '<nav><ul class="pagination justify-content-center">';
    for ($i = 1; $i <= $totalPages; $i++) {
        $active = $i === $currentPage ? ' active' : '';
        $sep = str_contains($baseUrl, '?') ? '&' : '?';
        $html .= '<li class="page-item' . $active . '"><a class="page-link" href="' . e($baseUrl . $sep . 'page=' . $i) . '">' . $i . '</a></li>';
    }
    $html .= '</ul></nav>';
    return $html;
}

function notifyUser(?int $userId, string $title, string $message, string $type = 'info'): void
{
    $db = getDB();
    $stmt = $db->prepare('INSERT INTO notifications (user_id, title, message, type) VALUES (?, ?, ?, ?)');
    $stmt->execute([$userId, $title, $message, $type]);
}

function notifyAllUsers(string $title, string $message, string $type = 'info'): void
{
    $db = getDB();
    $stmt = $db->query("SELECT id FROM users WHERE status = 'active'");
    while ($row = $stmt->fetch()) {
        notifyUser((int) $row['id'], $title, $message, $type);
    }
}

function getUnreadNotificationCount(?int $userId): int
{
    if (!$userId) {
        return 0;
    }
    $db = getDB();
    $stmt = $db->prepare('SELECT COUNT(*) FROM notifications WHERE user_id = ? AND is_read = 0');
    $stmt->execute([$userId]);
    return (int) $stmt->fetchColumn();
}

function generateCertificateForProperty(int $propertyId): bool
{
    $db = getDB();
    $check = $db->prepare('SELECT id FROM certificates WHERE property_id = ? AND status = "Valid"');
    $check->execute([$propertyId]);
    if ($check->fetch()) {
        return false;
    }

    $certNumber = generateUniqueNumber('CERT');
    $verifyUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http')
        . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost')
        . BASE_URL . '/verify.php?cert=' . urlencode($certNumber);

    $qrPath = generateQRCode($certNumber, $verifyUrl);

    $stmt = $db->prepare('INSERT INTO certificates (property_id, certificate_number, qr_code, issue_date, status) VALUES (?, ?, ?, CURDATE(), "Valid")');
    $stmt->execute([$propertyId, $certNumber, $qrPath]);
    logActivity('Certificate Generated', "Certificate $certNumber for property ID $propertyId");
    return true;
}

function generateQRCode(string $certNumber, string $verifyUrl): string
{
    $dir = UPLOAD_PATH . 'qr_codes/';
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    $filename = 'qr_' . preg_replace('/[^a-zA-Z0-9]/', '_', $certNumber) . '.png';
    $filepath = $dir . $filename;

    $vendorAutoload = APP_ROOT . '/vendor/autoload.php';
    if (file_exists($vendorAutoload)) {
        require_once $vendorAutoload;
        $qrCode = \Endroid\QrCode\QrCode::create($verifyUrl)
            ->setSize(200)
            ->setMargin(10);
        $writer = new \Endroid\QrCode\Writer\PngWriter();
        $result = $writer->write($qrCode);
        $result->saveToFile($filepath);
    } else {
        $apiUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=' . urlencode($verifyUrl);
        $imageData = @file_get_contents($apiUrl);
        if ($imageData) {
            file_put_contents($filepath, $imageData);
        }
    }

    return 'qr_codes/' . $filename;
}

function getCertificateDetails(string $certNumber): ?array
{
    $db = getDB();
    $sql = 'SELECT c.*, p.property_number, p.registration_number, p.ownership_date,
            o.full_name AS owner_name, o.national_id,
            l.plot_number, l.region, l.district, l.neighborhood, l.land_size, l.land_type, l.full_address
            FROM certificates c
            JOIN properties p ON c.property_id = p.id
            JOIN owners o ON p.owner_id = o.id
            JOIN lands l ON p.land_id = l.id
            WHERE c.certificate_number = ?';
    $stmt = $db->prepare($sql);
    $stmt->execute([$certNumber]);
    $row = $stmt->fetch();
    return $row ?: null;
}

function getOwnerByUserId(int $userId): ?array
{
    $db = getDB();
    $stmt = $db->prepare('SELECT * FROM owners WHERE user_id = ? LIMIT 1');
    $stmt->execute([$userId]);
    $row = $stmt->fetch();
    return $row ?: null;
}

function validateRequired(array $fields, array $data): array
{
    $errors = [];
    foreach ($fields as $field => $label) {
        if (empty(trim($data[$field] ?? ''))) {
            $errors[$field] = "$label is required";
        }
    }
    return $errors;
}

function getDateFilter(string $period, ?string $start = null, ?string $end = null): array
{
    $today = date('Y-m-d');
    switch ($period) {
        case 'daily':
            return [$today, $today];
        case 'weekly':
            return [date('Y-m-d', strtotime('-7 days')), $today];
        case 'monthly':
            return [date('Y-m-01'), $today];
        case 'yearly':
            return [date('Y-01-01'), $today];
        case 'custom':
            return [$start ?: $today, $end ?: $today];
        default:
            return ['1970-01-01', $today];
    }
}
