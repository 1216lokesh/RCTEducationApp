<?php
require_once __DIR__ . '/../../backend/includes/init.php';

Auth::requireLogin();
Auth::requireRole('patient');

$user = $auth->getCurrentUser();
$userId = intval($user['id']);

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $agree = isset($_POST['agree']);

    if (!$agree) {
        $error = 'Please check the consent box first';
    } else {
        $consent_date = date("Y-m-d");

        // Save consent in database
        $check = $db->fetchOne("SELECT id FROM consent WHERE user_id = {$userId}");
        if ($check) {
            $db->query("
                UPDATE consent 
                SET consent_given = 'yes', consent_date = '{$consent_date}', created_at = NOW() 
                WHERE user_id = {$userId}
            ");
        } else {
            $db->query("
                INSERT INTO consent (user_id, consent_given, consent_date, created_at)
                VALUES ({$userId}, 'yes', '{$consent_date}', NOW())
            ");
        }

        // Redirect to Satisfaction survey
        header('Location: ' . APP_URL . '/patient/satisfaction.php');
        exit;
    }
}

include __DIR__ . '/../views/header.php';
?>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            
            <div class="card shadow-sm border-0 rounded-3 p-4">
                <h3 class="fw-bold mb-3 text-primary-custom" style="color: #1565C0;">
                    <?php echo __('consent_title'); ?>
                </h3>
                <p class="fw-semibold text-dark mb-3"><?php echo __('consent_understand'); ?></p>

                <!-- Consent scrollable card -->
                <div class="card p-3 mb-4 text-muted bg-light border-0" style="max-height: 250px; overflow-y: auto; font-size: 0.95rem; line-height: 1.6;">
                    <?php 
                    $consent_text = __('consent_text');
                    // In PHP, Android's \n was replaced by actual newlines by our Python merger
                    echo nl2br(htmlspecialchars($consent_text));
                    ?>
                </div>

                <?php if ($error): ?>
                    <div class="alert alert-danger p-2 mb-3" style="font-size: 0.9rem;">
                        <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="">
                    
                    <label class="d-flex align-items-start gap-3 p-3 border rounded-3 mb-4 cursor-pointer" style="background-color: #fcfcfc;">
                        <input type="checkbox" name="agree" value="yes" class="mt-1" required>
                        <span style="font-size: 0.9rem; line-height: 1.4;"><?php echo __('consent_checkbox'); ?></span>
                    </label>

                    <button type="submit" class="btn w-100 py-2.5 text-white" style="background-color: #1565C0; border-radius: 8px;">
                        <?php echo __('i_agree') ?: 'I Agree - Proceed'; ?> <i class="fas fa-file-signature ms-1"></i>
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
</style>

<?php include __DIR__ . '/../views/footer.php'; ?>
