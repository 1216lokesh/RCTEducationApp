<?php
require_once __DIR__ . '/../../backend/includes/init.php';

Auth::requireLogin();
Auth::requireRole('patient');

$user = $auth->getCurrentUser();
$userId = intval($user['id']);
$apt = intval($_GET['apt'] ?? 1);
if ($apt < 1 || $apt > 4) {
    $apt = 1;
}

$error = '';
$showModal = false;
$submittedScore = 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $q1 = intval($_POST['q1'] ?? -1);
    $q2 = intval($_POST['q2'] ?? -1);
    $q3 = intval($_POST['q3'] ?? -1);

    if ($q1 === -1 || $q2 === -1 || $q3 === -1) {
        $error = __('please_answer') ?: 'Please answer all questions';
    } else {
        if ($apt === 4) {
            // Final assessment is a survey/questionnaire, no score is calculated
            // We just redirect directly to 1 week follow up
            header('Location: ' . APP_URL . '/patient/followup_1week.php');
            exit;
        } else {
            // Check correct answers
            $score = 0;
            if ($apt === 1) {
                if ($q1 === 1) $score++; // rb1b
                if ($q2 === 1) $score++; // rb2b
                if ($q3 === 1) $score++; // rb3b
            } elseif ($apt === 2) {
                if ($q1 === 1) $score++; // rb1b
                if ($q2 === 0) $score++; // rb2a
                if ($q3 === 1) $score++; // rb3b
            } elseif ($apt === 3) {
                if ($q1 === 0) $score++; // rb1a
                if ($q2 === 1) $score++; // rb2b
                if ($q3 === 0) $score++; // rb3a
            }

            // Save score to scores table in database
            $check = $db->fetchOne("SELECT id FROM scores WHERE user_id = {$userId}");
            $quiz_col = 'quiz' . $apt;
            if ($check) {
                $db->query("UPDATE scores SET {$quiz_col} = {$score} WHERE user_id = {$userId}");
            } else {
                $db->query("INSERT INTO scores (user_id, {$quiz_col}) VALUES ({$userId}, {$score})");
            }

            // If quiz 2 is completed, mark Appointment 2 completed in database (already handled by save_score logic or redirect logic)
            // Note: Appointment completion progress is computed dynamically in dashboard.php based on non-null values.

            $submittedScore = $score;
            $showModal = true;
        }
    }
}

// Extract strings
if ($apt === 4) {
    $title = __('final_title');
    $subtitle = __('final_subtitle');
    $q1_text = __('final_q1');
    $q2_text = __('final_q2');
    $q3_text = __('final_q3');
    $opts = [
        'q1' => [__('final_q1_a'), __('final_q1_b'), __('final_q1_c')],
        'q2' => [__('final_q2_a'), __('final_q2_b'), __('final_q2_c')],
        'q3' => [__('final_q3_a'), __('final_q3_b'), __('final_q3_c')]
    ];
    $btn_text = __('complete_assessment');
} else {
    $title = __("quiz{$apt}_title");
    $subtitle = __('apt1_subtitle');
    $q1_text = __("quiz{$apt}_q1");
    $q2_text = __("quiz{$apt}_q2");
    $q3_text = __("quiz{$apt}_q3");
    $opts = [
        'q1' => [__("quiz{$apt}_q1_a"), __("quiz{$apt}_q1_b"), __("quiz{$apt}_q1_c")],
        'q2' => [__("quiz{$apt}_q2_a"), __("quiz{$apt}_q2_b"), __("quiz{$apt}_q2_c")],
        'q3' => [__("quiz{$apt}_q3_a"), __("quiz{$apt}_q3_b"), __("quiz{$apt}_q3_c")]
    ];
    $btn_text = __('submit');
}

// Redirect URL after score confirmation
$redirectUrl = APP_URL . '/patient/dashboard.php';
if ($apt === 1) {
    $redirectUrl = APP_URL . '/patient/counselling.php';
} elseif ($apt === 2) {
    $redirectUrl = APP_URL . '/patient/postop.php';
}

include __DIR__ . '/../views/header.php';
?>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            
            <div class="card shadow-sm border-0 rounded-3 p-4">
                <h3 class="fw-bold mb-2 text-primary-custom" style="color: #1565C0;">
                    <?php echo htmlspecialchars($title); ?>
                </h3>
                <p class="text-muted mb-4"><?php echo htmlspecialchars($subtitle); ?></p>

                <?php if ($error): ?>
                    <div class="alert alert-danger p-2 mb-3" style="font-size: 0.9rem;">
                        <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="">
                    
                    <!-- Question 1 -->
                    <div class="mb-4">
                        <label class="form-label fw-semibold text-dark d-block mb-2"><?php echo htmlspecialchars($q1_text); ?></label>
                        <div class="d-flex flex-column gap-2">
                            <?php foreach ($opts['q1'] as $index => $optText): ?>
                                <label class="p-3 border rounded-3 d-flex align-items-center gap-3 cursor-pointer select-option-label" style="background-color: #fcfcfc;">
                                    <input type="radio" name="q1" value="<?php echo $index; ?>" required>
                                    <span><?php echo htmlspecialchars($optText); ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Question 2 -->
                    <div class="mb-4">
                        <label class="form-label fw-semibold text-dark d-block mb-2"><?php echo htmlspecialchars($q2_text); ?></label>
                        <div class="d-flex flex-column gap-2">
                            <?php foreach ($opts['q2'] as $index => $optText): ?>
                                <label class="p-3 border rounded-3 d-flex align-items-center gap-3 cursor-pointer select-option-label" style="background-color: #fcfcfc;">
                                    <input type="radio" name="q2" value="<?php echo $index; ?>" required>
                                    <span><?php echo htmlspecialchars($optText); ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Question 3 -->
                    <div class="mb-4">
                        <label class="form-label fw-semibold text-dark d-block mb-2"><?php echo htmlspecialchars($q3_text); ?></label>
                        <div class="d-flex flex-column gap-2">
                            <?php foreach ($opts['q3'] as $index => $optText): ?>
                                <label class="p-3 border rounded-3 d-flex align-items-center gap-3 cursor-pointer select-option-label" style="background-color: #fcfcfc;">
                                    <input type="radio" name="q3" value="<?php echo $index; ?>" required>
                                    <span><?php echo htmlspecialchars($optText); ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <button type="submit" class="btn w-100 py-2.5 text-white" style="background-color: #1565C0; border-radius: 8px;">
                        <?php echo htmlspecialchars($btn_text); ?> <i class="fas fa-check ms-1"></i>
                    </button>

                </form>
            </div>

        </div>
    </div>
</div>

<!-- Score Overlay Modal -->
<?php if ($showModal): ?>
    <?php
    $emoji = '💪';
    $message = __('quiz_msg_retry');
    if ($submittedScore === 3) {
        $emoji = '🎉';
        $message = __('quiz_msg_excellent');
    } elseif ($submittedScore === 2) {
        $emoji = '👍';
        $message = __('quiz_msg_good');
    } elseif ($submittedScore === 1) {
        $emoji = '📖';
        $message = __('quiz_msg_keep');
    }
    ?>
    <div class="position-fixed top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center" style="background-color: rgba(0,0,0,0.5); z-index: 9999;">
        <div class="card border-0 shadow-lg p-4 text-center rounded-3 mx-3" style="max-width: 400px; background-color: #ffffff; animation: zoomIn 0.3s ease;">
            <div style="font-size: 4rem;"><?php echo $emoji; ?></div>
            <h4 class="fw-bold my-2" style="color: #1565C0;"><?php echo __('quiz_result_title'); ?></h4>
            <h5 class="fw-bold mb-3"><?php echo __('quiz_score_label'); ?>: <?php echo $submittedScore; ?> / 3</h5>
            <p class="text-muted mb-4"><?php echo htmlspecialchars($message); ?></p>
            <a href="<?php echo $redirectUrl; ?>" class="btn w-100 text-white py-2" style="background-color: #1565C0; border-radius: 8px;">
                <?php echo __('quiz_continue'); ?>
            </a>
        </div>
    </div>
    <style>
    @keyframes zoomIn {
        from { transform: scale(0.9); opacity: 0; }
        to { transform: scale(1); opacity: 1; }
    }
    </style>
<?php endif; ?>

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
