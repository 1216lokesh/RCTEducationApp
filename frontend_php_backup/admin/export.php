<?php
require_once __DIR__ . '/../../backend/includes/init.php';

Auth::requireRole('admin');

$allowedTypes = ['patients', 'scores', 'attendance', 'consent'];
$downloadType = $_GET['download'] ?? '';

if ($downloadType && in_array($downloadType, $allowedTypes, true)) {
    switch ($downloadType) {
        case 'patients':
            $rows = $db->fetchAll("SELECT id, name, email, phone, status, created_at FROM users WHERE role = 'patient' ORDER BY created_at DESC");
            $filename = 'patients_export_' . date('Ymd') . '.csv';
            $headers = ['ID', 'Full Name', 'Email', 'Phone', 'Status', 'Registered At'];
            break;
        case 'scores':
            $rows = $db->fetchAll("SELECT s.id, u.name AS patient_name, s.quiz1, s.quiz2, s.quiz3, s.followup_1week FROM scores s LEFT JOIN users u ON s.user_id = u.id ORDER BY s.id DESC");
            $filename = 'scores_export_' . date('Ymd') . '.csv';
            $headers = ['ID', 'Patient Name', 'Quiz 1 Score', 'Quiz 2 Score', 'Quiz 3 Score', 'Follow Up Complete'];
            break;
        case 'attendance':
            $rows = $db->fetchAll("SELECT a.id, u.name AS patient_name, a.apt1, a.apt2, a.apt3, a.apt4, a.created_at FROM attendance a LEFT JOIN users u ON a.user_id = u.id ORDER BY a.created_at DESC");
            $filename = 'attendance_export_' . date('Ymd') . '.csv';
            $headers = ['ID', 'Patient Name', 'Appointment 1', 'Appointment 2', 'Appointment 3', 'Follow Up', 'Logged At'];
            break;
        case 'consent':
            $rows = $db->fetchAll("SELECT c.id, u.name AS patient_name, c.consent_given, c.consent_date, c.created_at FROM consent c LEFT JOIN users u ON c.user_id = u.id ORDER BY c.created_at DESC");
            $filename = 'consent_export_' . date('Ymd') . '.csv';
            $headers = ['ID', 'Patient Name', 'Consent Given', 'Consent Date', 'Logged At'];
            break;
    }

    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=' . $filename);
    $output = fopen('php://output', 'w');
    fputcsv($output, $headers);

    foreach ($rows as $row) {
        fputcsv($output, array_values($row));
    }

    fclose($output);
    exit;
}
?>

<?php include __DIR__ . '/../views/header.php'; ?>

<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="fw-bold mb-1" style="color: #1565C0;"><i class="fas fa-file-csv"></i> <?php echo __('export_data'); ?></h2>
            <p class="text-muted"><?php echo __('export_data_description') ?? 'Download patient records and reports as CSV files.'; ?></p>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="btn-group flex-wrap shadow-sm rounded-3" role="group">
                <a href="<?php echo APP_URL; ?>/admin/dashboard.php" class="btn btn-outline-primary px-4 py-2.5" style="border-color: #e2e2e2; color: #1565C0; font-weight: 500;">
                    <i class="fas fa-th-large me-1"></i> Dashboard
                </a>
                <a href="<?php echo APP_URL; ?>/admin/patients.php" class="btn btn-outline-primary px-4 py-2.5" style="border-color: #e2e2e2; color: #1565C0; font-weight: 500;">
                    <i class="fas fa-users me-1"></i> <?php echo __('patient_list'); ?>
                </a>
                <a href="<?php echo APP_URL; ?>/admin/scores.php" class="btn btn-outline-primary px-4 py-2.5" style="border-color: #e2e2e2; color: #1565C0; font-weight: 500;">
                    <i class="fas fa-chart-bar me-1"></i> <?php echo __('view_scores'); ?>
                </a>
                <a href="<?php echo APP_URL; ?>/admin/consent.php" class="btn btn-outline-primary px-4 py-2.5" style="border-color: #e2e2e2; color: #1565C0; font-weight: 500;">
                    <i class="fas fa-check me-1"></i> <?php echo __('view_consent_status'); ?>
                </a>
                <a href="<?php echo APP_URL; ?>/admin/attendance.php" class="btn btn-outline-primary px-4 py-2.5" style="border-color: #e2e2e2; color: #1565C0; font-weight: 500;">
                    <i class="fas fa-clipboard-list me-1"></i> <?php echo __('view_attendance'); ?>
                </a>
                <a href="<?php echo APP_URL; ?>/admin/export.php" class="btn text-white px-4 py-2.5 active" style="background-color: #1565C0; font-weight: 500;">
                    <i class="fas fa-download me-1"></i> <?php echo __('export_data'); ?>
                </a>
            </div>
        </div>
    </div>

    <!-- Export Action Cards -->
    <div class="row g-3">
        <?php 
        $exportOptions = [
            'patients' => ['label' => __('patient_list'), 'icon' => 'fa-users'],
            'scores' => ['label' => __('view_scores'), 'icon' => 'fa-chart-bar'],
            'attendance' => ['label' => __('view_attendance'), 'icon' => 'fa-clipboard-list'],
            'consent' => ['label' => __('view_consent_status'), 'icon' => 'fa-check']
        ];
        foreach ($exportOptions as $type => $opt): 
        ?>
        <div class="col-md-6 col-xl-3">
            <div class="card shadow-sm h-100 border-0 rounded-3 overflow-hidden p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0 text-dark"><?php echo htmlspecialchars($opt['label']); ?></h5>
                    <i class="fas <?php echo $opt['icon']; ?> fa-2x opacity-25 text-primary-custom" style="color: #1565C0;"></i>
                </div>
                <p class="text-muted small mb-4"><?php echo __('export_collection_description') ?? 'Download the latest records for this section.'; ?></p>
                <a href="<?php echo APP_URL; ?>/admin/export.php?download=<?php echo $type; ?>" class="btn text-white w-100 mt-auto py-2" style="background-color: #1565C0; border-radius: 6px;">
                    <i class="fas fa-download me-1"></i> <?php echo __('download'); ?>
                </a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<?php include __DIR__ . '/../views/footer.php'; ?>
