<?php
require_once __DIR__ . '/../../backend/includes/init.php';

Auth::requireLogin();
Auth::requireRole('patient');

$user = $auth->getCurrentUser();
$userId = intval($user['id']);

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $q1 = trim($_POST['q1'] ?? '');
    $q2 = trim($_POST['q2'] ?? '');
    $q3 = trim($_POST['q3'] ?? '');

    if ($q1 === '' || $q2 === '' || $q3 === '') {
        $error = __('please_answer_all') ?: 'Please answer all questions';
    } else {
        // Save followup adherence response in postop_adherence table (if it exists)
        // Wait! Let's check: in the Android API, it saves:
        // followed_instructions = q2, had_complications = q1, complication_details = q3?
        // Wait, let's look at get_my_procedure or save_postop.php:
        // Actually, FollowUp1WeekActivity just hits save_score.php with quiz = followup_1week and score = 1!
        // Let's do exactly that:
        $check = $db->fetchOne("SELECT id FROM scores WHERE user_id = {$userId}");
        if ($check) {
            $db->query("UPDATE scores SET followup_1week = 1 WHERE user_id = {$userId}");
        } else {
            $db->query("INSERT INTO scores (user_id, followup_1week) VALUES ({$userId}, 1)");
        }

        // Redirect back to dashboard
        header('Location: ' . APP_URL . '/patient/dashboard.php');
        exit;
    }
}

$opts = [
    'q1' => [__('followup1week_q1_a'), __('followup1week_q1_b'), __('followup1week_q1_c')],
    'q2' => [__('followup1week_q2_a'), __('followup1week_q2_b'), __('followup1week_q2_c')],
    'q3' => [__('followup1week_q3_a'), __('followup1week_q3_b'), __('followup1week_q3_c')]
];

include __DIR__ . '/../views/header.php';
?>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            
            <div class="card shadow-sm border-0 rounded-3 p-4">
                <h3 class="fw-bold mb-2 text-primary-custom" style="color: #1565C0;">
                    <?php echo __('followup1week_title'); ?>
                </h3>
                <p class="text-muted mb-4"><?php echo __('followup1week_subtitle'); ?></p>

                <?php if ($error): ?>
                    <div class="alert alert-danger p-2 mb-3" style="font-size: 0.9rem;">
                        <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="">
                    
                    <!-- Question 1 -->
                    <div class="mb-4">
                        <label class="form-label fw-semibold text-dark d-block mb-2"><?php echo __('followup1week_q1'); ?></label>
                        <div class="d-flex flex-column gap-2">
                            <?php foreach ($opts['q1'] as $index => $optText): ?>
                                <label class="p-3 border rounded-3 d-flex align-items-center gap-3 cursor-pointer select-option-label" style="background-color: #fcfcfc;">
                                    <input type="radio" name="q1" value="<?php echo htmlspecialchars($optText); ?>" required>
                                    <span><?php echo htmlspecialchars($optText); ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Question 2 -->
                    <div class="mb-4">
                        <label class="form-label fw-semibold text-dark d-block mb-2"><?php echo __('followup1week_q2'); ?></label>
                        <div class="d-flex flex-column gap-2">
                            <?php foreach ($opts['q2'] as $index => $optText): ?>
                                <label class="p-3 border rounded-3 d-flex align-items-center gap-3 cursor-pointer select-option-label" style="background-color: #fcfcfc;">
                                    <input type="radio" name="q2" value="<?php echo htmlspecialchars($optText); ?>" required>
                                    <span><?php echo htmlspecialchars($optText); ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Question 3 -->
                    <div class="mb-4">
                        <label class="form-label fw-semibold text-dark d-block mb-2"><?php echo __('followup1week_q3'); ?></label>
                        <div class="d-flex flex-column gap-2">
                            <?php foreach ($opts['q3'] as $index => $optText): ?>
                                <label class="p-3 border rounded-3 d-flex align-items-center gap-3 cursor-pointer select-option-label" style="background-color: #fcfcfc;">
                                    <input type="radio" name="q3" value="<?php echo htmlspecialchars($optText); ?>" required>
                                    <span><?php echo htmlspecialchars($optText); ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <button type="submit" class="btn w-100 py-2.5 text-white" style="background-color: #2E7D32; border-radius: 8px;">
                        <?php echo __('submit'); ?> <i class="fas fa-check ms-1"></i>
                    </button>

                </form>
            </div>

        </div>
    </div>
</div>

<style>
.cursor-pointer {
    cursor: pointer;
    transition: all 0.2s ease;
}
.cursor-pointer:hover {
    background-color: #f0f7ff !important;
    border-color: #1565C0 !important;
}
input[type="radio"]:checked + span {
    font-weight: 600;
    color: #1565C0;
}
</style>

<?php include __DIR__ . '/../views/footer.php'; ?>
