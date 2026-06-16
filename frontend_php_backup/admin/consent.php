<?php
require_once __DIR__ . '/../../backend/includes/init.php';

Auth::requireRole('admin');

$results = $db->fetchAll("
    SELECT c.id, c.consent_given, c.consent_date, c.created_at, u.id as patient_id, u.name, u.email
    FROM consent c
    INNER JOIN users u ON c.user_id = u.id
    ORDER BY c.created_at DESC
    LIMIT 100
");
?>

<?php include __DIR__ . '/../views/header.php'; ?>

<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="fw-bold mb-1" style="color: #1565C0;"><i class="fas fa-file-contract"></i> <?php echo __('consent_status'); ?></h2>
            <p class="text-muted"><?php echo __('view_consent_description') ?? 'Review digital consent acceptance for recent appointments.'; ?></p>
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
                <a href="<?php echo APP_URL; ?>/admin/consent.php" class="btn text-white px-4 py-2.5 active" style="background-color: #1565C0; font-weight: 500;">
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

    <!-- Consent Records Table -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0 rounded-3 overflow-hidden">
                <div class="card-header text-white p-3" style="background-color: #1565C0;">
                    <h5 class="mb-0 fw-bold"><?php echo __('consent_status'); ?></h5>
                </div>
                <div class="card-body p-0">
                    <?php if (count($results) > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-4">#</th>
                                        <th>Patient Name</th>
                                        <th>Email Address</th>
                                        <th>Consent Given</th>
                                        <th>Consent Date</th>
                                        <th>Signature Logged At</th>
                                        <th class="pe-4 text-end">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($results as $index => $row): ?>
                                        <tr>
                                            <td class="ps-4 fw-semibold text-muted"><?php echo $index + 1; ?></td>
                                            <td class="fw-semibold text-dark"><?php echo htmlspecialchars($row['name']); ?></td>
                                            <td><?php echo htmlspecialchars($row['email']); ?></td>
                                            <td>
                                                <span class="badge bg-success" style="font-weight: 500;">
                                                    <i class="fas fa-check-double"></i> <?php echo htmlspecialchars(ucfirst($row['consent_given'])); ?>
                                                </span>
                                            </td>
                                            <td><?php echo date('d M, Y', strtotime($row['consent_date'])); ?></td>
                                            <td><?php echo date('d M, Y H:i:s', strtotime($row['created_at'])); ?></td>
                                            <td class="pe-4 text-end">
                                                <a href="<?php echo APP_URL; ?>/admin/patient-detail.php?id=<?php echo $row['patient_id']; ?>" class="btn btn-sm text-white px-3" style="background-color: #1565C0; border-radius: 6px;">
                                                    <i class="fas fa-eye"></i> View Detail
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info m-4 mb-4">
                            <i class="fas fa-info-circle"></i> <?php echo __('no_records_found') ?? 'No consent signatures captured yet.'; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../views/footer.php'; ?>
