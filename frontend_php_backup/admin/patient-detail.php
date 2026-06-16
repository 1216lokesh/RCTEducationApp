<?php
require_once __DIR__ . '/../../backend/includes/init.php';

Auth::requireRole('admin');

$adminUser = $auth->getCurrentUser();
$patientId = intval($_GET['id'] ?? 0);

// Load patient profile
$patient = $db->fetchOne("SELECT id, name, email, phone, status, created_at FROM users WHERE id = {$patientId} AND role = 'patient'");
if (!$patient) {
    header('Location: ' . APP_URL . '/admin/patients.php');
    exit;
}

// Handle procedure assignment form submission
$successMsg = '';
$errorMsg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['assign_procedure'])) {
    $procId = intval($_POST['procedure_id'] ?? 0);
    $groupType = trim($_POST['group_type'] ?? '');

    if ($procId <= 0 || !in_array($groupType, ['Intervention', 'Comparator'], true)) {
        $errorMsg = 'Please select a valid procedure and clinical group.';
    } else {
        $date = date("Y-m-d");
        $adminId = intval($adminUser['id']);

        // Check if procedure already assigned
        $check = $db->fetchOne("SELECT id FROM patient_procedure WHERE patient_id = {$patientId}");
        if ($check) {
            $db->query("
                UPDATE patient_procedure 
                SET procedure_id = {$procId}, assigned_by = {$adminId}, group_type = '{$groupType}', assigned_date = '{$date}' 
                WHERE patient_id = {$patientId}
            ");
        } else {
            $db->query("
                INSERT INTO patient_procedure (patient_id, procedure_id, assigned_by, group_type, assigned_date)
                VALUES ({$patientId}, {$procId}, {$adminId}, '{$groupType}', '{$date}')
            ");
        }
        $successMsg = 'Procedure assigned successfully!';
    }
}

// Handle password reset form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reset_password'])) {
    $newPass = trim($_POST['new_password'] ?? '');
    if (empty($newPass)) {
        $errorMsg = 'Please enter a valid password.';
    } else {
        $hashedPass = password_hash($newPass, PASSWORD_BCRYPT);
        $result = $db->update('users', ['password' => $hashedPass], "id = {$patientId}");
        if ($result['success']) {
            $successMsg = 'Patient password reset successfully!';
        } else {
            $errorMsg = 'Failed to reset password: ' . $result['error'];
        }
    }
}

// Fetch assigned procedure
$assignedProc = $db->fetchOne("
    SELECT pp.procedure_id, pp.group_type, pp.assigned_date, pr.name, pr.category, pr.description
    FROM patient_procedure pp
    JOIN procedures pr ON pp.procedure_id = pr.id
    WHERE pp.patient_id = {$patientId}
    LIMIT 1
");

// Fetch procedures list
$proceduresList = $db->fetchAll("SELECT id, name, category FROM procedures ORDER BY name ASC");

// Fetch consent logs
$consentLog = $db->fetchOne("SELECT consent_given, consent_date FROM consent WHERE user_id = {$patientId}");

// Fetch scores logs
$scoresLog = $db->fetchOne("SELECT quiz1, quiz2, quiz3, followup_1week FROM scores WHERE user_id = {$patientId}");

// Fetch attendance logs
$attendanceLog = $db->fetchOne("SELECT apt1, apt2, apt3, apt4 FROM attendance WHERE user_id = {$patientId}");

// Fetch baseline responses
$baselineResponses = $db->fetchAll("SELECT appointment, q1, q2, q3, created_at FROM baseline_responses WHERE patient_id = {$patientId} ORDER BY created_at ASC");

// Fetch anxiety scores
$anxietyScores = $db->fetchAll("SELECT timepoint, score FROM anxiety_scores WHERE patient_id = {$patientId} ORDER BY timepoint ASC");

// Fetch satisfaction scores
$satisfactionScores = $db->fetchAll("SELECT timepoint, score FROM satisfaction_scores WHERE patient_id = {$patientId} ORDER BY timepoint ASC");
?>

<?php include __DIR__ . '/../views/header.php'; ?>

<div class="container-fluid py-4">
    <!-- Back Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex align-items-center justify-content-between gap-3 flex-wrap">
                <div>
                    <h2 class="fw-bold mb-1" style="color: #1565C0;"><i class="fas fa-user-circle"></i> Patient: <?php echo htmlspecialchars($patient['name']); ?></h2>
                    <p class="text-muted mb-0">Clinical assessment logs and procedure controls</p>
                </div>
                <a href="<?php echo APP_URL; ?>/admin/patients.php" class="btn btn-outline-secondary btn-sm px-3" style="border-radius: 6px;">
                    <i class="fas fa-arrow-left me-1"></i> Back to Patient List
                </a>
            </div>
        </div>
    </div>

    <?php if ($successMsg): ?>
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($successMsg); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if ($errorMsg): ?>
        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
            <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($errorMsg); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row g-4">
        <!-- Patient Details Profile & Assignment controls -->
        <div class="col-lg-4 col-md-5">
            
            <!-- Profile Info Card -->
            <div class="card shadow-sm border-0 rounded-3 p-4 mb-4">
                <h5 class="fw-bold mb-3" style="color: #1565C0;"><i class="fas fa-address-card me-1"></i> Profile Overview</h5>
                <p class="mb-2 text-dark"><strong>Name:</strong> <?php echo htmlspecialchars($patient['name']); ?></p>
                <p class="mb-2 text-dark"><strong>Email:</strong> <?php echo htmlspecialchars($patient['email']); ?></p>
                <p class="mb-2 text-dark"><strong>Phone:</strong> <?php echo htmlspecialchars($patient['phone'] ?: 'No Phone'); ?></p>
                <p class="mb-2 text-dark"><strong>Status:</strong> <span class="badge bg-<?php echo $patient['status'] === 'active' ? 'success' : 'secondary'; ?>"><?php echo ucfirst($patient['status']); ?></span></p>
                <p class="mb-0 text-dark"><strong>Registered:</strong> <?php echo date('d M, Y', strtotime($patient['created_at'])); ?></p>
            </div>

            <!-- Procedure Assignment Card -->
            <div class="card shadow-sm border-0 rounded-3 p-4">
                <h5 class="fw-bold mb-3" style="color: #1565C0;"><i class="fas fa-notes-medical me-1"></i> Assign Procedure</h5>
                
                <?php if ($assignedProc): ?>
                    <div class="alert alert-primary p-3 border-0 rounded-3 mb-4" style="background-color: #E3F2FD; color: #1565C0;">
                        <h6 class="fw-bold mb-1"><i class="fas fa-file-medical"></i> Currently Assigned:</h6>
                        <p class="mb-1 fw-bold"><?php echo htmlspecialchars($assignedProc['name']); ?></p>
                        <p class="mb-1 text-muted" style="font-size: 0.85rem;"><i class="fas fa-clinic-medical"></i> Category: <?php echo htmlspecialchars($assignedProc['category']); ?></p>
                        <p class="mb-0 text-muted" style="font-size: 0.85rem;"><i class="fas fa-users"></i> Group: <strong><?php echo htmlspecialchars($assignedProc['group_type']); ?></strong></p>
                        <span class="d-block text-muted mt-2" style="font-size: 0.75rem;">Assigned on <?php echo date('d M, Y', strtotime($assignedProc['assigned_date'])); ?></span>
                    </div>
                <?php endif; ?>

                <form method="POST" action="">
                    <input type="hidden" name="assign_procedure" value="1">
                    
                    <div class="mb-3">
                        <label class="form-label text-dark fw-semibold">Select Clinical Procedure</label>
                        <select name="procedure_id" class="form-select form-select-sm" required style="border-radius: 6px;">
                            <option value="">-- Choose Procedure --</option>
                            <?php foreach ($proceduresList as $proc): ?>
                                <option value="<?php echo $proc['id']; ?>" <?php echo ($assignedProc && $assignedProc['procedure_id'] == $proc['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($proc['name']); ?> (<?php echo htmlspecialchars($proc['category']); ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-dark fw-semibold">Clinical Group Assignment</label>
                        <select name="group_type" class="form-select form-select-sm" required style="border-radius: 6px;">
                            <option value="">-- Choose Group --</option>
                            <option value="Intervention" <?php echo ($assignedProc && $assignedProc['group_type'] === 'Intervention') ? 'selected' : ''; ?>>Intervention Group (App Education)</option>
                            <option value="Comparator" <?php echo ($assignedProc && $assignedProc['group_type'] === 'Comparator') ? 'selected' : ''; ?>>Standard Care Group</option>
                        </select>
                    </div>

                    <button type="submit" class="btn text-white w-100 py-2 btn-sm shadow-sm" style="background-color: #1565C0; border-radius: 8px;">
                        <i class="fas fa-save me-1"></i> Update Assignment
                    </button>
                </form>
            </div>

            <!-- Reset Password Card -->
            <div class="card shadow-sm border-0 rounded-3 p-4 mt-4">
                <h5 class="fw-bold mb-3" style="color: #1565C0;"><i class="fas fa-key me-1"></i> Reset Password</h5>
                <form method="POST" action="">
                    <input type="hidden" name="reset_password" value="1">
                    
                    <div class="mb-3">
                        <label class="form-label text-dark fw-semibold">New Password</label>
                        <div class="input-group input-group-sm">
                            <input type="password" name="new_password" id="new_password" class="form-control" placeholder="Enter new password" required style="border-radius: 6px 0 0 6px;">
                            <button class="btn btn-outline-secondary" type="button" id="togglePasswordBtn" style="border-radius: 0 6px 6px 0; border: 1.5px solid #e2e8f0; border-left: none;"><i class="fas fa-eye"></i></button>
                        </div>
                    </div>

                    <button type="submit" class="btn text-white w-100 py-2 btn-sm shadow-sm" style="background-color: #d32f2f; border-radius: 8px;">
                        <i class="fas fa-sync-alt me-1"></i> Reset Password
                    </button>
                </form>
            </div>

            <script>
            document.getElementById('togglePasswordBtn').addEventListener('click', function() {
                const pwdInput = document.getElementById('new_password');
                const eyeIcon = this.querySelector('i');
                if (pwdInput.type === 'password') {
                    pwdInput.type = 'text';
                    eyeIcon.classList.remove('fa-eye');
                    eyeIcon.classList.add('fa-eye-slash');
                } else {
                    pwdInput.type = 'password';
                    eyeIcon.classList.remove('fa-eye-slash');
                    eyeIcon.classList.add('fa-eye');
                }
            });
            </script>

        </div>

        <!-- Clinical Journey Tracker Logs -->
        <div class="col-lg-8 col-md-7">
            
            <!-- Scores & Attendance Tracker -->
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <div class="card shadow-sm border-0 rounded-3 p-4 h-100">
                        <h6 class="fw-bold mb-3" style="color: #1565C0;"><i class="fas fa-poll-h me-1"></i> Quiz Completion scores</h6>
                        <ul class="list-group list-group-flush" style="font-size: 0.95rem;">
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <span>Appointment 1 Quiz:</span>
                                <span class="fw-bold"><?php echo ($scoresLog && $scoresLog['quiz1'] !== null) ? $scoresLog['quiz1'] . ' / 3' : '<span class="text-muted">Not Taken</span>'; ?></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <span>Appointment 2 Quiz:</span>
                                <span class="fw-bold"><?php echo ($scoresLog && $scoresLog['quiz2'] !== null) ? $scoresLog['quiz2'] . ' / 3' : '<span class="text-muted">Not Taken</span>'; ?></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <span>Appointment 3 Quiz:</span>
                                <span class="fw-bold"><?php echo ($scoresLog && $scoresLog['quiz3'] !== null) ? $scoresLog['quiz3'] . ' / 3' : '<span class="text-muted">Not Taken</span>'; ?></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <span>Follow Up quiz:</span>
                                <span class="fw-bold"><?php echo ($scoresLog && $scoresLog['followup_1week'] !== null) ? 'Completed ✅' : '<span class="text-muted">Not Taken</span>'; ?></span>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card shadow-sm border-0 rounded-3 p-4 h-100">
                        <h6 class="fw-bold mb-3" style="color: #1565C0;"><i class="fas fa-calendar-check me-1"></i> Attendance Records</h6>
                        <ul class="list-group list-group-flush" style="font-size: 0.95rem;">
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <span>Appointment 1:</span>
                                <span class="badge bg-<?php echo ($attendanceLog && $attendanceLog['apt1'] === 'present') ? 'success' : 'secondary'; ?>">
                                    <?php echo ($attendanceLog && $attendanceLog['apt1'] === 'present') ? 'Present' : 'Absent'; ?>
                                </span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <span>Appointment 2:</span>
                                <span class="badge bg-<?php echo ($attendanceLog && $attendanceLog['apt2'] === 'present') ? 'success' : 'secondary'; ?>">
                                    <?php echo ($attendanceLog && $attendanceLog['apt2'] === 'present') ? 'Present' : 'Absent'; ?>
                                </span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <span>Appointment 3:</span>
                                <span class="badge bg-<?php echo ($attendanceLog && $attendanceLog['apt3'] === 'present') ? 'success' : 'secondary'; ?>">
                                    <?php echo ($attendanceLog && $attendanceLog['apt3'] === 'present') ? 'Present' : 'Absent'; ?>
                                </span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <span>Follow Up Visit:</span>
                                <span class="badge bg-<?php echo ($attendanceLog && $attendanceLog['apt4'] === 'present') ? 'success' : 'secondary'; ?>">
                                    <?php echo ($attendanceLog && $attendanceLog['apt4'] === 'present') ? 'Present' : 'Absent'; ?>
                                </span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Questionnaire Baseline Responses Log -->
            <div class="card shadow-sm border-0 rounded-3 p-4 mb-4">
                <h5 class="fw-bold mb-3" style="color: #1565C0;"><i class="fas fa-clipboard-list me-1"></i> Baseline Questionnaire Answers</h5>
                <?php if (count($baselineResponses) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" style="font-size: 0.9rem;">
                            <thead class="table-light">
                                <tr>
                                    <th>Appointment</th>
                                    <th>Question 1 Answer</th>
                                    <th>Question 2 Answer</th>
                                    <th>Question 3 Answer</th>
                                    <th>Submitted Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($baselineResponses as $resp): ?>
                                    <tr>
                                        <td class="fw-semibold text-dark"><?php echo ucfirst(str_replace('apt', 'Apt ', $resp['appointment'])); ?></td>
                                        <td><?php echo htmlspecialchars($resp['q1']); ?></td>
                                        <td><?php echo htmlspecialchars($resp['q2']); ?></td>
                                        <td><?php echo htmlspecialchars($resp['q3']); ?></td>
                                        <td><?php echo date('d M, Y', strtotime($resp['created_at'])); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-light border-0 mb-0 py-2">
                        <i class="fas fa-info-circle text-muted"></i> <span class="text-muted">No baseline survey responses submitted yet.</span>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Anxiety & Satisfaction Scores Logs -->
            <div class="row g-3">
                <!-- Anxiety History -->
                <div class="col-md-6">
                    <div class="card shadow-sm border-0 rounded-3 p-4 h-100">
                        <h6 class="fw-bold mb-3" style="color: #1565C0;"><i class="fas fa-heartbeat me-1"></i> Anxiety Levels History</h6>
                        <?php if (count($anxietyScores) > 0): ?>
                            <ul class="list-group list-group-flush" style="font-size: 0.9rem;">
                                <?php foreach ($anxietyScores as $anx): ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                        <span><?php echo ucfirst(str_replace('apt', 'Apt ', $anx['timepoint'])); ?>:</span>
                                        <span class="badge rounded-pill px-2.5 py-1 text-white <?php echo $anx['score'] > 10 ? 'bg-danger' : ($anx['score'] > 5 ? 'bg-warning text-dark' : 'bg-success'); ?>" style="font-size: 0.8rem; font-weight: 500;">
                                            Score: <?php echo $anx['score']; ?> / 15
                                        </span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <p class="text-muted small mb-0"><i class="fas fa-info-circle"></i> No anxiety logs captured yet.</p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Satisfaction History -->
                <div class="col-md-6">
                    <div class="card shadow-sm border-0 rounded-3 p-4 h-100">
                        <h6 class="fw-bold mb-3" style="color: #1565C0;"><i class="fas fa-smile me-1"></i> App Satisfaction Survey</h6>
                        <?php if (count($satisfactionScores) > 0): ?>
                            <ul class="list-group list-group-flush" style="font-size: 0.9rem;">
                                <?php foreach ($satisfactionScores as $sat): ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                        <span><?php echo ucfirst(str_replace('_', ' ', $sat['timepoint'])); ?>:</span>
                                        <span class="badge rounded-pill bg-success px-2.5 py-1" style="font-size: 0.8rem; font-weight: 500;">
                                            Score: <?php echo $sat['score']; ?> / 25
                                        </span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <p class="text-muted small mb-0"><i class="fas fa-info-circle"></i> No satisfaction logs captured yet.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<?php include __DIR__ . '/../views/footer.php'; ?>
