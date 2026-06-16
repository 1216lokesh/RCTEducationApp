<?php
require_once __DIR__ . '/../../backend/includes/init.php';

Auth::requireLogin();
Auth::requireRole('patient');

$user = $auth->getCurrentUser();
$userId = intval($user['id']);
$apt = intval($_GET['apt'] ?? 1);
if ($apt < 1 || $apt > 3) {
    $apt = 1;
}

// Get assigned procedure ID
$patient_procedure = $db->fetchOne("SELECT procedure_id FROM patient_procedure WHERE patient_id = {$userId} LIMIT 1");
$procedureId = $patient_procedure ? intval($patient_procedure['procedure_id']) : 1;

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $answers = [
        intval($_POST['q1'] ?? -1),
        intval($_POST['q2'] ?? -1),
        intval($_POST['q3'] ?? -1),
        intval($_POST['q4'] ?? -1),
        intval($_POST['q5'] ?? -1),
    ];

    if (in_array(-1, $answers, true)) {
        $error = __('please_answer') ?: 'Please answer all questions';
    } else {
        $score = array_sum($answers);
        $timepoint = 'apt' . $apt;

        // Save to database
        $check = $db->fetchOne("
            SELECT id FROM anxiety_scores 
            WHERE patient_id = {$userId} AND procedure_id = {$procedureId} AND timepoint = '{$timepoint}'
        ");

        if ($check) {
            $db->query("
                UPDATE anxiety_scores SET score = {$score} 
                WHERE patient_id = {$userId} AND procedure_id = {$procedureId} AND timepoint = '{$timepoint}'
            ");
        } else {
            $db->query("
                INSERT INTO anxiety_scores (patient_id, procedure_id, timepoint, score)
                VALUES ({$userId}, {$procedureId}, '{$timepoint}', {$score})
            ");
        }

        // Redirect to Quiz
        header('Location: ' . APP_URL . '/patient/quiz.php?apt=' . $apt);
        exit;
    }
}

$questions = [
    __('anxiety_q1'),
    __('anxiety_q2'),
    __('anxiety_q3'),
    __('anxiety_q4'),
    __('anxiety_q5')
];

$opts = [
    0 => __('anxiety_opt1'), // Not at all
    1 => __('anxiety_opt2'), // Slightly
    2 => __('anxiety_opt3'), // Moderately
    3 => __('anxiety_opt4')  // Very much
];

include __DIR__ . '/../views/header.php';
?>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            
            <div class="card shadow-sm border-0 rounded-3 p-4">
                <h3 class="fw-bold mb-2 text-primary-custom" style="color: #1565C0;">
                    <?php echo __('anxiety_title'); ?>
                </h3>
                <p class="text-muted mb-4"><?php echo __('anxiety_subtitle'); ?></p>

                <?php if ($error): ?>
                    <div class="alert alert-danger p-2 mb-3" style="font-size: 0.9rem;">
                        <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="">
                    
                    <?php foreach ($questions as $qIndex => $qText): ?>
                        <div class="mb-4">
                            <label class="form-label fw-semibold text-dark d-block mb-2">
                                <?php echo htmlspecialchars($qText); ?>
                            </label>
                            <div class="d-flex flex-column gap-2">
                                <?php foreach ($opts as $scoreVal => $optText): ?>
                                    <label class="p-3 border rounded-3 d-flex align-items-center gap-3 cursor-pointer select-option-label" style="background-color: #fcfcfc;">
                                        <input type="radio" name="q<?php echo ($qIndex + 1); ?>" value="<?php echo $scoreVal; ?>" required>
                                        <span><?php echo htmlspecialchars($optText); ?></span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>

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
