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
                <h3 class="fw-bold mb-4 text-primary-custom" style="color: #1565C0;">
                    <?php echo __('counselling_title'); ?>
                </h3>

                <!-- Section 1 -->
                <div class="mb-4">
                    <h5 class="fw-bold text-dark mb-2"><?php echo __('counselling_h1'); ?></h5>
                    <p class="text-muted" style="line-height: 1.6;"><?php echo __('counselling_p1'); ?></p>
                </div>

                <!-- Section 2 -->
                <div class="mb-4">
                    <h5 class="fw-bold text-dark mb-2"><?php echo __('counselling_h2'); ?></h5>
                    <p class="text-muted" style="line-height: 1.6;"><?php echo __('counselling_p2'); ?></p>
                </div>

                <!-- Section 3 -->
                <div class="mb-4">
                    <h5 class="fw-bold text-dark mb-2"><?php echo __('counselling_h3'); ?></h5>
                    <p class="text-muted" style="line-height: 1.6;"><?php echo __('counselling_p3'); ?></p>
                </div>

                <a href="<?php echo APP_URL; ?>/patient/consent.php" class="btn w-100 py-2.5 text-white" style="background-color: #1565C0; border-radius: 8px;">
                    <?php echo __('counselling_btn'); ?> <i class="fas fa-arrow-right ms-1"></i>
                </a>
            </div>

        </div>
    </div>
</div>

<?php include __DIR__ . '/../views/footer.php'; ?>
