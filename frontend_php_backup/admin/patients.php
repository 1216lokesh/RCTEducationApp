<?php
require_once __DIR__ . '/../../backend/includes/init.php';

Auth::requireRole('admin');

$search = trim($_GET['search'] ?? '');
$where = "WHERE role = 'patient'";
if ($search !== '') {
    $escapedSearch = $db->escape('%' . $search . '%');
    $where .= " AND (name LIKE '{$escapedSearch}' OR email LIKE '{$escapedSearch}' OR phone LIKE '{$escapedSearch}')";
}

$patients = $db->fetchAll("SELECT id, name, email, phone, status, created_at FROM users {$where} ORDER BY created_at DESC LIMIT 50");
?>

<?php include __DIR__ . '/../views/header.php'; ?>

<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex align-items-center justify-content-between gap-3 flex-wrap">
                <div>
                    <h2 class="fw-bold mb-1" style="color: #1565C0;"><i class="fas fa-users"></i> <?php echo __('patient_list'); ?></h2>
                    <p class="text-muted mb-0"><?php echo __('manage_patients_description') ?? 'View and manage registered patients'; ?></p>
                </div>
                <form class="d-flex align-items-center gap-2" method="get" action="<?php echo APP_URL; ?>/admin/patients.php">
                    <input type="text" name="search" class="form-control form-control-sm" placeholder="<?php echo __('search'); ?>" value="<?php echo htmlspecialchars($search); ?>" style="border-radius: 6px;">
                    <button type="submit" class="btn text-white btn-sm px-3" style="background-color: #1565C0; border-radius: 6px;"><i class="fas fa-search"></i></button>
                </form>
            </div>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="btn-group flex-wrap shadow-sm rounded-3" role="group">
                <a href="<?php echo APP_URL; ?>/admin/dashboard.php" class="btn btn-outline-primary px-4 py-2.5" style="border-color: #e2e2e2; color: #1565C0; font-weight: 500;">
                    <i class="fas fa-th-large me-1"></i> Dashboard
                </a>
                <a href="<?php echo APP_URL; ?>/admin/patients.php" class="btn text-white px-4 py-2.5 active" style="background-color: #1565C0; font-weight: 500;">
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

    <!-- Patients List Table -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0 rounded-3 overflow-hidden">
                <div class="card-header text-white p-3" style="background-color: #1565C0;">
                    <h5 class="mb-0 fw-bold"><?php echo __('all_patients'); ?></h5>
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
                                        <th>Status</th>
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
                                            <td>
                                                <span class="badge bg-<?php echo $patient['status'] === 'active' ? 'success' : ($patient['status'] === 'inactive' ? 'secondary' : 'danger'); ?>" style="border-radius: 4px; font-weight: 500;">
                                                    <?php echo htmlspecialchars(ucfirst($patient['status'])); ?>
                                                </span>
                                            </td>
                                            <td><?php echo date('d M, Y', strtotime($patient['created_at'])); ?></td>
                                            <td class="pe-4 text-end">
                                                <a href="<?php echo APP_URL; ?>/admin/patient-detail.php?id=<?php echo $patient['id']; ?>" class="btn btn-sm text-white px-3" style="background-color: #1565C0; border-radius: 6px;">
                                                    <i class="fas fa-eye"></i> View Details
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info m-4 mb-4">
                            <i class="fas fa-info-circle"></i> <?php echo __('no_records_found') ?? 'No patients found.'; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../views/footer.php'; ?>
