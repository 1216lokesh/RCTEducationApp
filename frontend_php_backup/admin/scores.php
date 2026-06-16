<?php
require_once __DIR__ . '/../../backend/includes/init.php';

Auth::requireRole('admin');

$results = $db->fetchAll("
    SELECT u.id, u.name, u.email,
           s.quiz1, s.quiz2, s.quiz3, s.followup_1week,
           (SELECT MAX(score) FROM anxiety_scores WHERE patient_id = u.id) as max_anxiety,
           (SELECT MAX(score) FROM satisfaction_scores WHERE patient_id = u.id) as max_satisfaction
    FROM users u
    LEFT JOIN scores s ON u.id = s.user_id
    WHERE u.role = 'patient'
    ORDER BY u.created_at DESC
    LIMIT 100
");
?>

<?php include __DIR__ . '/../views/header.php'; ?>

<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="fw-bold mb-1" style="color: #1565C0;"><i class="fas fa-chart-bar"></i> <?php echo __('view_scores'); ?></h2>
            <p class="text-muted"><?php echo __('view_scores_description') ?? 'Review assessment progress across patients.'; ?></p>
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
                <a href="<?php echo APP_URL; ?>/admin/scores.php" class="btn text-white px-4 py-2.5 active" style="background-color: #1565C0; font-weight: 500;">
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

    <!-- Scores List Table -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0 rounded-3 overflow-hidden">
                <div class="card-header text-white p-3" style="background-color: #1565C0;">
                    <h5 class="mb-0 fw-bold"><?php echo __('assessment_scores'); ?></h5>
                </div>
                <div class="card-body p-0">
                    <?php if (count($results) > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-4">#</th>
                                        <th>Patient Name</th>
                                        <th>Quiz 1 Score</th>
                                        <th>Quiz 2 Score</th>
                                        <th>Quiz 3 Score</th>
                                        <th>Follow Up</th>
                                        <th>Max Anxiety</th>
                                        <th>Satisfaction</th>
                                        <th class="pe-4 text-end">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($results as $index => $row): ?>
                                        <tr>
                                            <td class="ps-4 fw-semibold text-muted"><?php echo $index + 1; ?></td>
                                            <td class="fw-semibold text-dark"><?php echo htmlspecialchars($row['name']); ?></td>
                                            <td><?php echo ($row['quiz1'] !== null) ? $row['quiz1'] . ' / 3' : '<span class="text-muted">-</span>'; ?></td>
                                            <td><?php echo ($row['quiz2'] !== null) ? $row['quiz2'] . ' / 3' : '<span class="text-muted">-</span>'; ?></td>
                                            <td><?php echo ($row['quiz3'] !== null) ? $row['quiz3'] . ' / 3' : '<span class="text-muted">-</span>'; ?></td>
                                            <td><?php echo ($row['followup_1week'] !== null) ? '<span class="badge bg-success" style="font-weight:500;">Done</span>' : '<span class="text-muted">-</span>'; ?></td>
                                            <td>
                                                <?php if ($row['max_anxiety'] !== null): ?>
                                                    <span class="badge rounded-pill text-white <?php echo $row['max_anxiety'] > 10 ? 'bg-danger' : ($row['max_anxiety'] > 5 ? 'bg-warning text-dark' : 'bg-success'); ?>" style="font-size: 0.8rem; font-weight: 500;">
                                                        <?php echo $row['max_anxiety']; ?> / 15
                                                    </span>
                                                <?php else: ?>
                                                    <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($row['max_satisfaction'] !== null): ?>
                                                    <span class="badge rounded-pill bg-success px-2.5 py-1" style="font-size: 0.8rem; font-weight: 500;">
                                                        <?php echo $row['max_satisfaction']; ?> / 25
                                                    </span>
                                                <?php else: ?>
                                                    <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="pe-4 text-end">
                                                <a href="<?php echo APP_URL; ?>/admin/patient-detail.php?id=<?php echo $row['id']; ?>" class="btn btn-sm text-white px-3" style="background-color: #1565C0; border-radius: 6px;">
                                                    <i class="fas fa-eye"></i> View Profile
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info m-4 mb-4">
                            <i class="fas fa-info-circle"></i> <?php echo __('no_records_found') ?? 'No assessment scores available yet.'; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../views/footer.php'; ?>
