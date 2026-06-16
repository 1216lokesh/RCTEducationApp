<?php
require_once __DIR__ . '/../../backend/includes/init.php';

Auth::requireRole('admin');

$adminUser = $auth->getCurrentUser();

// Count stats in rct_app database
$totalPatients = $db->fetchOne("SELECT COUNT(*) as count FROM users WHERE role = 'patient'")['count'];
$totalAssigned = $db->fetchOne("SELECT COUNT(*) as count FROM patient_procedure")['count'];

// Count step completions
$consentCount = $db->fetchOne("SELECT COUNT(*) as count FROM consent WHERE consent_given = 'yes'")['count'];

$scores = $db->fetchAll("SELECT quiz2, quiz3, followup_1week FROM scores");
$quiz2Count = 0;
$quiz3Count = 0;
$followupCount = 0;
foreach ($scores as $s) {
    if ($s['quiz2'] !== null) $quiz2Count++;
    if ($s['quiz3'] !== null) $quiz3Count++;
    if ($s['followup_1week'] !== null) $followupCount++;
}

$completedSteps = $consentCount + $quiz2Count + $quiz3Count + $followupCount;
$totalExpectedSteps = $totalPatients * 4;

// Get patient list
$patients = $db->fetchAll("SELECT id, name, email, phone, created_at FROM users WHERE role = 'patient' ORDER BY created_at DESC LIMIT 10");
?>

<?php include __DIR__ . '/../views/header.php'; ?>

<div class="container-fluid py-4">
    <!-- Welcome Section -->
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="fw-bold mb-1" style="color: #1565C0;"><i class="fas fa-chart-line"></i> <?php echo __('admin_dashboard'); ?></h2>
            <p class="text-muted"><?php echo __('admin_welcome_message') ?? 'Manage patients and track treatment progress'; ?></p>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4 g-3">
        <div class="col-md-3">
            <div class="card text-white shadow-sm" style="background: linear-gradient(135deg, #1565C0 0%, #003d82 100%); border: 0;">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-1 text-white-50" style="font-size: 0.9rem; font-weight: 500;"><?php echo __('patient_list'); ?></h6>
                            <h2 class="mb-0 fw-bold"><?php echo $totalPatients; ?></h2>
                        </div>
                        <i class="fas fa-users fa-3x opacity-25"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card text-white shadow-sm" style="background: linear-gradient(135deg, #0288D1 0%, #005b94 100%); border: 0;">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-1 text-white-50" style="font-size: 0.9rem; font-weight: 500;">Procedures Assigned</h6>
                            <h2 class="mb-0 fw-bold"><?php echo $totalAssigned; ?></h2>
                        </div>
                        <i class="fas fa-notes-medical fa-3x opacity-25"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card text-white shadow-sm" style="background: linear-gradient(135deg, #2E7D32 0%, #1b5e20 100%); border: 0;">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-1 text-white-50" style="font-size: 0.9rem; font-weight: 500;">Step Completions</h6>
                            <h2 class="mb-0 fw-bold"><?php echo $completedSteps; ?> / <?php echo $totalExpectedSteps; ?></h2>
                        </div>
                        <i class="fas fa-check-circle fa-3x opacity-25"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card text-white shadow-sm" style="background: linear-gradient(135deg, #f57c00 0%, #b25300 100%); border: 0;">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-1 text-white-50" style="font-size: 0.9rem; font-weight: 500;"><?php echo __('digital_consent'); ?></h6>
                            <h2 class="mb-0 fw-bold"><?php echo $consentCount; ?></h2>
                        </div>
                        <i class="fas fa-file-contract fa-3x opacity-25"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="btn-group flex-wrap shadow-sm rounded-3" role="group">
                <a href="<?php echo APP_URL; ?>/admin/dashboard.php" class="btn text-white px-4 py-2.5 active" style="background-color: #1565C0; font-weight: 500;">
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
                <a href="<?php echo APP_URL; ?>/admin/export.php" class="btn btn-outline-primary px-4 py-2.5" style="border-color: #e2e2e2; color: #1565C0; font-weight: 500;">
                    <i class="fas fa-download me-1"></i> <?php echo __('export_data'); ?>
                </a>
            </div>
        </div>
    </div>

    <!-- Recent Patients -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0 rounded-3 overflow-hidden">
                <div class="card-header text-white p-3" style="background-color: #1565C0;">
                    <h5 class="mb-0 fw-bold"><i class="fas fa-clock me-1"></i> Recent Patients</h5>
                </div>
                <div class="card-body p-0">
                    <?php if (count($patients) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">#</th>
                                    <th>Patient Name</th>
                                    <th>Email Address</th>
                                    <th>Phone Number</th>
                                    <th>Registered Date</th>
                                    <th class="pe-4 text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($patients as $index => $patient): ?>
                                <tr>
                                    <td class="ps-4 fw-semibold text-muted"><?php echo $index + 1; ?></td>
                                    <td class="fw-semibold text-dark"><?php echo htmlspecialchars($patient['name']); ?></td>
                                    <td><?php echo htmlspecialchars($patient['email']); ?></td>
                                    <td><?php echo htmlspecialchars($patient['phone'] ?: 'No Phone'); ?></td>
                                    <td><?php echo date('d M, Y', strtotime($patient['created_at'])); ?></td>
                                    <td class="pe-4 text-end">
                                        <a href="<?php echo APP_URL; ?>/admin/patient-detail.php?id=<?php echo $patient['id']; ?>" 
                                           class="btn btn-sm text-white px-3" style="background-color: #1565C0; border-radius: 6px;">
                                            <i class="fas fa-eye"></i> View Detail
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php else: ?>
                    <div class="alert alert-info m-4">
                        <i class="fas fa-info-circle"></i> No patients registered yet.
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../views/footer.php'; ?>
