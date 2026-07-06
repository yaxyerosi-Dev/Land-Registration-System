<?php
declare(strict_types=1);
require_once dirname(__DIR__, 2) . '/config/config.php';
requireAdmin();

$type = $_GET['type'] ?? 'lands';
$period = $_GET['period'] ?? 'monthly';
$start = $_GET['start'] ?? null;
$end = $_GET['end'] ?? null;
[$dateFrom, $dateTo] = getDateFilter($period, $start, $end);
$db = getDB();

$reports = [
    'lands' => ['title' => __('land_report'), 'sql' => 'SELECT * FROM lands WHERE registration_date BETWEEN ? AND ? ORDER BY id DESC', 'headers' => ['Plot','Land No','Region','District','Type','Status','Date']],
    'owners' => ['title' => __('owner_report'), 'sql' => 'SELECT * FROM owners WHERE registration_date BETWEEN ? AND ? ORDER BY id DESC', 'headers' => ['Name','National ID','Phone','Email','Date']],
    'properties' => ['title' => __('property_report'), 'sql' => 'SELECT p.*, o.full_name AS owner_name, l.plot_number FROM properties p JOIN owners o ON p.owner_id=o.id JOIN lands l ON p.land_id=l.id WHERE p.ownership_date BETWEEN ? AND ? ORDER BY p.id DESC', 'headers' => ['Property No','Reg No','Owner','Plot','Status','Date']],
    'transfers' => ['title' => __('transfer_report'), 'sql' => 'SELECT t.*, p.property_number FROM ownership_transfers t JOIN properties p ON t.property_id=p.id WHERE t.transfer_date BETWEEN ? AND ? ORDER BY t.id DESC', 'headers' => ['Property','Status','Date','Reason']],
    'certificates' => ['title' => __('certificate_report'), 'sql' => 'SELECT c.*, p.property_number FROM certificates c JOIN properties p ON c.property_id=p.id WHERE c.issue_date BETWEEN ? AND ? ORDER BY c.id DESC', 'headers' => ['Cert No','Property','Status','Issue Date']],
];

$config = $reports[$type] ?? $reports['lands'];
$stmt = $db->prepare($config['sql']);
$stmt->execute([$dateFrom, $dateTo]);
$data = $stmt->fetchAll();

if (isset($_GET['export'])) {
    if ($_GET['export'] === 'excel') {
        exportExcel($config['title'], $config['headers'], $data);
    } elseif ($_GET['export'] === 'pdf') {
        exportPdfReport($config['title'], $config['headers'], $data, $dateFrom, $dateTo);
    }
    exit;
}

$pageTitle = __('reports');
$isAdminArea = true;
include APP_ROOT . '/includes/header.php';
?>
<div class="page-header"><h1><?= __('reports') ?></h1></div>
<div class="card mb-3"><div class="card-body">
<form method="GET" class="row g-2 align-items-end">
<div class="col-md-3"><label class="form-label">Report Type</label><select name="type" class="form-select"><?php foreach(array_keys($reports) as $k): ?><option value="<?= $k ?>" <?= $type===$k?'selected':'' ?>><?= e($reports[$k]['title']) ?></option><?php endforeach; ?></select></div>
<div class="col-md-3"><label class="form-label"><?= __('filter') ?></label><select name="period" class="form-select" id="periodSelect"><option value="daily" <?= $period==='daily'?'selected':'' ?>><?= __('daily') ?></option><option value="weekly" <?= $period==='weekly'?'selected':'' ?>><?= __('weekly') ?></option><option value="monthly" <?= $period==='monthly'?'selected':'' ?>><?= __('monthly') ?></option><option value="yearly" <?= $period==='yearly'?'selected':'' ?>><?= __('yearly') ?></option><option value="custom" <?= $period==='custom'?'selected':'' ?>><?= __('custom') ?></option></select></div>
<div class="col-md-2 custom-dates" style="display:<?= $period==='custom'?'block':'none' ?>"><label class="form-label"><?= __('date_from') ?></label><input type="date" name="start" class="form-control" value="<?= e($dateFrom) ?>"></div>
<div class="col-md-2 custom-dates" style="display:<?= $period==='custom'?'block':'none' ?>"><label class="form-label"><?= __('date_to') ?></label><input type="date" name="end" class="form-control" value="<?= e($dateTo) ?>"></div>
<div class="col-md-2"><button class="btn btn-primary w-100"><?= __('filter') ?></button></div>
</form>
<div class="mt-2">
<a href="?type=<?= e($type) ?>&period=<?= e($period) ?>&start=<?= e($dateFrom) ?>&end=<?= e($dateTo) ?>&export=pdf" class="btn btn-sm btn-danger"><?= __('export_pdf') ?></a>
<a href="?type=<?= e($type) ?>&period=<?= e($period) ?>&start=<?= e($dateFrom) ?>&end=<?= e($dateTo) ?>&export=excel" class="btn btn-sm btn-success"><?= __('export_excel') ?></a>
</div>
</div></div>
<div class="card"><div class="card-header"><?= e($config['title']) ?> (<?= e($dateFrom) ?> — <?= e($dateTo) ?>)</div>
<div class="table-responsive"><table class="table table-hover mb-0"><thead><tr><?php foreach($config['headers'] as $h): ?><th><?= e($h) ?></th><?php endforeach; ?></tr></thead><tbody>
<?php foreach ($data as $row): ?>
<tr><?php foreach ($row as $key => $val): if (is_numeric($key)) continue; if (in_array($key, ['id','owner_id','land_id','property_id','user_id','current_owner_id','new_owner_id','qr_code','photo','full_address','admin_remark','transfer_reason','created_at'])) continue; ?><td><?= e((string)$val) ?></td><?php endforeach; ?></tr>
<?php endforeach; ?>
<?php if(empty($data)): ?><tr><td colspan="10" class="text-center text-muted py-4"><?= __('no_records') ?></td></tr><?php endif; ?>
</tbody></table></div></div>
<script>document.getElementById('periodSelect').addEventListener('change',function(){document.querySelectorAll('.custom-dates').forEach(el=>el.style.display=this.value==='custom'?'block':'none');});</script>
<?php include APP_ROOT . '/includes/footer.php'; ?>

<?php
function exportExcel(string $title, array $headers, array $data): void
{
    $vendorAutoload = APP_ROOT . '/vendor/autoload.php';
    if (!file_exists($vendorAutoload)) {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="report.csv"');
        echo implode(',', $headers) . "\n";
        foreach ($data as $row) {
            echo implode(',', array_map(fn($v) => '"' . str_replace('"', '""', (string)$v) . '"', array_values($row))) . "\n";
        }
        return;
    }
    require_once $vendorAutoload;
    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle(substr($title, 0, 31));
    foreach ($headers as $i => $h) {
        $sheet->setCellValueByColumnAndRow($i + 1, 1, $h);
    }
    $rowNum = 2;
    foreach ($data as $row) {
        $col = 1;
        foreach ($row as $key => $val) {
            if (is_numeric($key)) continue;
            $sheet->setCellValueByColumnAndRow($col++, $rowNum, (string)$val);
        }
        $rowNum++;
    }
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="report.xlsx"');
    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
    $writer->save('php://output');
}

function exportPdfReport(string $title, array $headers, array $data, string $from, string $to): void
{
    $html = '<h2>' . htmlspecialchars($title) . '</h2><p>Period: ' . htmlspecialchars($from) . ' to ' . htmlspecialchars($to) . '</p>';
    $html .= '<table border="1" cellpadding="5" cellspacing="0" width="100%"><tr>';
    foreach ($headers as $h) $html .= '<th>' . htmlspecialchars($h) . '</th>';
    $html .= '</tr>';
    foreach ($data as $row) {
        $html .= '<tr>';
        foreach ($row as $key => $val) {
            if (is_numeric($key)) continue;
            if (in_array($key, ['id','owner_id','land_id','property_id','qr_code','photo','full_address','admin_remark','created_at'])) continue;
            $html .= '<td>' . htmlspecialchars((string)$val) . '</td>';
        }
        $html .= '</tr>';
    }
    $html .= '</table>';
    $vendorAutoload = APP_ROOT . '/vendor/autoload.php';
    if (file_exists($vendorAutoload)) {
        require_once $vendorAutoload;
        $dompdf = new \Dompdf\Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        $dompdf->stream('report.pdf', ['Attachment' => true]);
    } else {
        header('Content-Type: text/html');
        echo $html . '<script>window.print()</script>';
    }
}
