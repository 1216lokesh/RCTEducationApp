<?php
require_once __DIR__ . '/../../backend/includes/init.php';

Auth::requireLogin();
Auth::requireRole('patient');

include __DIR__ . '/../views/header.php';
?>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            
            <div class="card shadow-sm border-0 rounded-3 p-4">
                <h3 class="fw-bold mb-2 text-primary-custom" style="color: #1565C0;">
                    <?php echo __('postop_title'); ?>
                </h3>
                <p class="text-muted mb-4"><?php echo __('postop_subtitle'); ?></p>

                <div class="d-flex flex-column gap-3 mb-4">
                    
                    <!-- Instruction 1 -->
                    <div class="d-flex align-items-start gap-3">
                        <span class="d-flex align-items-center justify-content-center text-white fw-bold rounded-circle" style="background-color: #1565C0; width: 32px; height: 32px; flex-shrink: 0;">1</span>
                        <p class="mb-0 text-dark" style="line-height: 1.5; padding-top: 3px;"><?php echo __('postop_1'); ?></p>
                    </div>

                    <!-- Instruction 2 -->
                    <div class="d-flex align-items-start gap-3">
                        <span class="d-flex align-items-center justify-content-center text-white fw-bold rounded-circle" style="background-color: #1565C0; width: 32px; height: 32px; flex-shrink: 0;">2</span>
                        <p class="mb-0 text-dark" style="line-height: 1.5; padding-top: 3px;"><?php echo __('postop_2'); ?></p>
                    </div>

                    <!-- Instruction 3 -->
                    <div class="d-flex align-items-start gap-3">
                        <span class="d-flex align-items-center justify-content-center text-white fw-bold rounded-circle" style="background-color: #1565C0; width: 32px; height: 32px; flex-shrink: 0;">3</span>
                        <p class="mb-0 text-dark" style="line-height: 1.5; padding-top: 3px;"><?php echo __('postop_3'); ?></p>
                    </div>

                    <!-- Instruction 4 -->
                    <div class="d-flex align-items-start gap-3">
                        <span class="d-flex align-items-center justify-content-center text-white fw-bold rounded-circle" style="background-color: #1565C0; width: 32px; height: 32px; flex-shrink: 0;">4</span>
                        <p class="mb-0 text-dark" style="line-height: 1.5; padding-top: 3px;"><?php echo __('postop_4'); ?></p>
                    </div>

                    <!-- Instruction 5 -->
                    <div class="d-flex align-items-start gap-3">
                        <span class="d-flex align-items-center justify-content-center text-white fw-bold rounded-circle" style="background-color: #1565C0; width: 32px; height: 32px; flex-shrink: 0;">5</span>
                        <p class="mb-0 text-dark" style="line-height: 1.5; padding-top: 3px;"><?php echo __('postop_5'); ?></p>
                    </div>

                </div>

                <a href="<?php echo APP_URL; ?>/patient/dashboard.php" class="btn w-100 py-2.5 text-white" style="background-color: #1565C0; border-radius: 8px;">
                    <?php echo __('postop_next'); ?> <i class="fas fa-arrow-right ms-1"></i>
                </a>
            </div>

        </div>
    </div>
</div>

<?php include __DIR__ . '/../views/footer.php'; ?>
