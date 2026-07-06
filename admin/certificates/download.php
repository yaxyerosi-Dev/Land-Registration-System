<?php
declare(strict_types=1);
require_once dirname(__DIR__, 2) . '/config/config.php';
requireLogin();

$certNumber = $_GET['cert'] ?? '';
$id = (int)($_GET['id'] ?? 0);
$db = getDB();

if ($certNumber) {
    $cert = getCertificateDetails($certNumber);
} elseif ($id) {
    $stmt = $db->prepare('SELECT certificate_number FROM certificates WHERE property_id = ? AND status = "Valid" ORDER BY id DESC LIMIT 1');
    $stmt->execute([$id]);
    $cn = $stmt->fetchColumn();
    $cert = $cn ? getCertificateDetails($cn) : null;
} else {
    redirect(BASE_URL . '/admin/certificates/index.php');
}

if (!$cert) { flash('danger', __('no_records')); redirect(BASE_URL . '/admin/certificates/index.php'); }

if (!isAdmin()) {
    $owner = getOwnerByUserId((int)currentUser()['id']);
    $ownerCheck = $db->prepare('SELECT owner_id FROM properties WHERE id = ?');
    $ownerCheck->execute([(int)$cert['property_id']]);
    $propOwnerId = (int)$ownerCheck->fetchColumn();
    if (!$owner || (int)$owner['id'] !== $propOwnerId) {
        flash('danger', __('access_denied'));
        redirect(BASE_URL . '/user/dashboard.php');
    }
}

$settings = getSettings();
$html = buildCertificateHtml($cert, $settings);

$vendorAutoload = APP_ROOT . '/vendor/autoload.php';
if (file_exists($vendorAutoload)) {
    require_once $vendorAutoload;
    $dompdf = new \Dompdf\Dompdf(['isRemoteEnabled' => true]);
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();
    $dompdf->stream('certificate_' . $cert['certificate_number'] . '.pdf', ['Attachment' => true]);
} else {
    header('Content-Type: text/html; charset=utf-8');
    echo $html . '<script>window.print()</script>';
}
exit;

function buildCertificateHtml(array $cert, array $settings): string
{
    $qrUrl = $cert['qr_code'] ? UPLOAD_URL . $cert['qr_code'] : '';
    $logo = !empty($settings['logo']) ? UPLOAD_URL . $settings['logo'] : '';
    return '<!DOCTYPE html><html><head><meta charset="UTF-8"><style>
    body{font-family:Georgia,serif;margin:40px;color:#333}
    .cert{border:4px double #D4A017;padding:40px;max-width:700px;margin:0 auto}
    .header{text-align:center;border-bottom:2px solid #006D77;padding-bottom:20px;margin-bottom:30px}
    .header h1{color:#006D77;margin:0}.header h2{color:#0B2545;margin:5px 0}
    .field{margin:12px 0}.label{font-weight:bold;color:#0B2545}
    .footer{margin-top:40px;text-align:center;border-top:1px solid #ccc;padding-top:20px}
    .qr{text-align:center;margin-top:20px}
    </style></head><body><div class="cert">
    <div class="header">' . ($logo ? '<img src="' . $logo . '" height="60"><br>' : '') . '
    <h1>' . htmlspecialchars($settings['system_name']) . '</h1>
    <h2>CERTIFICATE OF LAND OWNERSHIP</h2>
    <p>' . htmlspecialchars($settings['office_name'] ?? '') . '</p></div>
    <p style="text-align:center;font-size:18px"><strong>Certificate No: ' . htmlspecialchars($cert['certificate_number']) . '</strong></p>
    <div class="field"><span class="label">Owner Name:</span> ' . htmlspecialchars($cert['owner_name']) . '</div>
    <div class="field"><span class="label">National ID:</span> ' . htmlspecialchars($cert['national_id']) . '</div>
    <div class="field"><span class="label">Property Number:</span> ' . htmlspecialchars($cert['property_number']) . '</div>
    <div class="field"><span class="label">Registration Number:</span> ' . htmlspecialchars($cert['registration_number']) . '</div>
    <div class="field"><span class="label">Plot Number:</span> ' . htmlspecialchars($cert['plot_number']) . '</div>
    <div class="field"><span class="label">Region:</span> ' . htmlspecialchars($cert['region']) . '</div>
    <div class="field"><span class="label">District:</span> ' . htmlspecialchars($cert['district']) . '</div>
    <div class="field"><span class="label">Neighborhood:</span> ' . htmlspecialchars($cert['neighborhood']) . '</div>
    <div class="field"><span class="label">Land Size:</span> ' . htmlspecialchars($cert['land_size']) . '</div>
    <div class="field"><span class="label">Land Type:</span> ' . htmlspecialchars($cert['land_type']) . '</div>
    <div class="field"><span class="label">Issue Date:</span> ' . htmlspecialchars($cert['issue_date']) . '</div>
    <div class="qr">' . ($qrUrl ? '<img src="' . $qrUrl . '" width="120"><br><small>Scan to verify</small>' : '') . '</div>
    <div class="footer"><p>This certificate is issued by the authorized land registration office.</p>
    <p>' . htmlspecialchars($settings['office_address'] ?? '') . ' | ' . htmlspecialchars($settings['office_phone'] ?? '') . '</p></div>
    </div></body></html>';
}
