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

$apt_col = 'apt' . $apt;
$apt_name = 'apt' . $apt;

// 1. Log attendance on page load
$checkAttendance = $db->fetchOne("SELECT id FROM attendance WHERE user_id = {$userId}");
if ($checkAttendance) {
    $db->query("UPDATE attendance SET {$apt_col} = 'present' WHERE user_id = {$userId}");
} else {
    $db->query("INSERT INTO attendance (user_id, {$apt_col}) VALUES ({$userId}, 'present')");
}

// 2. Handle questionnaire form submission
$message = '';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $q1 = trim($_POST['q1'] ?? '');
    $q2 = trim($_POST['q2'] ?? '');
    $q3 = trim($_POST['q3'] ?? '');

    if ($q1 === '' || $q2 === '' || $q3 === '') {
        $error = __('please_answer_all') ?: 'Please answer all questions';
    } else {
        // Insert responses into database
        $db->query("
            INSERT INTO baseline_responses (patient_id, appointment, q1, q2, q3, created_at)
            VALUES ({$userId}, '{$apt_name}', '{$db->escape($q1)}', '{$db->escape($q2)}', '{$db->escape($q3)}', NOW())
        ");

        // Redirect based on appointment step
        if ($apt === 1) {
            header('Location: ' . APP_URL . '/patient/procedure_info.php');
        } else {
            header('Location: ' . APP_URL . '/patient/education.php?apt=' . $apt);
        }
        exit;
    }
}

// Extract string resources based on appointment number
$title_key = "apt{$apt}_baseline_title";
$subtitle_key = "apt1_subtitle"; // The subtitle key is common in strings.xml
$q1_key = "apt{$apt}_q1";
$q2_key = "apt{$apt}_q2";
$q3_key = "apt{$apt}_q3";

// Determine options keys
if ($apt === 1) {
    $options = [
        'q1' => [__('apt1_q1_a'), __('apt1_q1_b'), __('apt1_q1_c')],
        'q2' => [__('apt1_q2_a'), __('apt1_q2_b'), __('apt1_q2_c')],
        'q3' => [__('apt1_q3_a'), __('apt1_q3_b'), __('apt1_q3_c')],
    ];
} elseif ($apt === 2) {
    $options = [
        'q1' => [__('apt2_q1_a'), __('apt2_q1_b'), __('apt2_q1_c')],
        'q2' => [__('apt2_q2_a'), __('apt2_q2_b'), __('apt2_q2_c')],
        'q3' => [__('apt2_q3_a'), __('apt2_q3_b'), __('apt2_q3_c')],
    ];
} elseif ($apt === 3) {
    $options = [
        'q1' => [__('apt3_q1_a'), __('apt3_q1_b'), __('apt3_q1_c')],
        'q2' => [__('apt3_q2_a'), __('apt3_q2_b'), __('apt3_q2_c')],
        'q3' => [__('apt3_q3_a'), __('apt3_q3_b'), __('apt3_q3_c')],
    ];
} else {
    $options = [
        'q1' => [__('apt4_q1_a'), __('apt4_q1_b'), __('apt4_q1_c')],
        'q2' => [__('apt4_q2_a'), __('apt4_q2_b'), __('apt4_q2_c')],
        'q3' => [__('apt4_q3_a'), __('apt4_q3_b'), __('apt4_q3_c')],
    ];
}

include __DIR__ . '/../views/header.php';
?>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            
            <div class="card shadow-sm border-0 rounded-3 p-4">
                <h3 class="fw-bold mb-2 text-primary-custom" style="color: #1565C0;"><?php echo __($title_key); ?></h3>
                <p class="text-muted mb-4"><?php echo __($subtitle_key); ?></p>

                <?php if ($error): ?>
                    <div class="alert alert-danger p-2 mb-3" style="font-size: 0.9rem;">
                        <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="">
                    
                    <!-- Question 1 -->
                    <div class="mb-4">
                        <label class="form-label fw-semibold text-dark d-block mb-2"><?php echo __($q1_key); ?></label>
                        <div class="d-flex flex-column gap-2">
                            <?php foreach ($options['q1'] as $index => $option_text): ?>
                                <label class="p-3 border rounded-3 d-flex align-items-center gap-3 cursor-pointer select-option-label" style="background-color: #fcfcfc;">
                                    <input type="radio" name="q1" value="<?php echo htmlspecialchars($option_text); ?>" required>
                                    <span><?php echo htmlspecialchars($option_text); ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Question 2 -->
                    <div class="mb-4">
                        <label class="form-label fw-semibold text-dark d-block mb-2"><?php echo __($q2_key); ?></label>
                        <div class="d-flex flex-column gap-2">
                            <?php foreach ($options['q2'] as $index => $option_text): ?>
                                <label class="p-3 border rounded-3 d-flex align-items-center gap-3 cursor-pointer select-option-label" style="background-color: #fcfcfc;">
                                    <input type="radio" name="q2" value="<?php echo htmlspecialchars($option_text); ?>" required>
                                    <span><?php echo htmlspecialchars($option_text); ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Question 3 -->
                    <div class="mb-4">
                        <label class="form-label fw-semibold text-dark d-block mb-2"><?php echo __($q3_key); ?></label>
                        <div class="d-flex flex-column gap-2">
                            <?php foreach ($options['q3'] as $index => $option_text): ?>
                                <label class="p-3 border rounded-3 d-flex align-items-center gap-3 cursor-pointer select-option-label" style="background-color: #fcfcfc;">
                                    <input type="radio" name="q3" value="<?php echo htmlspecialchars($option_text); ?>" required>
                                    <span><?php echo htmlspecialchars($option_text); ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <button type="submit" class="btn w-100 py-2.5 text-white" style="background-color: #1565C0; border-radius: 8px;">
                        <?php echo __('next'); ?> <i class="fas fa-arrow-right ms-1"></i>
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
