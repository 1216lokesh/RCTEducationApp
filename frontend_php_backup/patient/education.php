<?php
require_once __DIR__ . '/../../backend/includes/init.php';

Auth::requireLogin();
Auth::requireRole('patient');

$apt = intval($_GET['apt'] ?? 1);
if ($apt < 1 || $apt > 4) {
    $apt = 1;
}

// Map video IDs
$videoIds = [
    1 => 'oZU9Wd_cpYY',
    2 => '81qSdFYKRcc',
    3 => 'VXTJPFRzkvk',
    4 => '' // No video for reinforcement
];

$videoId = $videoIds[$apt] ?? '';

// Determine localization keys
if ($apt === 4) {
    $title_key = 'reinforcement_title';
    $h1_key = 'reinforcement_h1';
    $p1_key = 'reinforcement_p1';
    $h2_key = 'reinforcement_h2';
    $p2_key = 'reinforcement_p2';
    $h3_key = 'reinforcement_h3';
    $p3_key = 'reinforcement_p3';
    $btn_text_key = 'final_assessment_btn';
    $next_url = APP_URL . '/patient/quiz.php?apt=4';
} else {
    $title_key = "edu{$apt}_title";
    $h1_key = "edu{$apt}_h1";
    $p1_key = "edu{$apt}_p1";
    $h2_key = "edu{$apt}_h2";
    $p2_key = "edu{$apt}_p2";
    $h3_key = "edu{$apt}_h3";
    $p3_key = "edu{$apt}_p3";
    $btn_text_key = 'take_quiz';
    $next_url = APP_URL . '/patient/anxiety.php?apt=' . $apt;
}

include __DIR__ . '/../views/header.php';
?>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            
            <div class="card shadow-sm border-0 rounded-3 overflow-hidden mb-4">
                
                <!-- Video Thumbnail Block -->
                <?php if ($videoId): ?>
                    <a href="https://www.youtube.com/watch?v=<?php echo $videoId; ?>" target="_blank" class="position-relative d-block video-thumbnail-link">
                        <img src="https://img.youtube.com/vi/<?php echo $videoId; ?>/hqdefault.jpg" class="w-100" style="height: 250px; object-fit: cover;" alt="Video Thumbnail">
                        <div class="position-absolute top-50 start-50 translate-middle play-btn-overlay">
                            <div class="d-flex align-items-center justify-content-center bg-danger rounded-circle text-white shadow" style="width: 68px; height: 68px;">
                                <i class="fas fa-play fa-2x ms-1"></i>
                            </div>
                        </div>
                    </a>
                <?php endif; ?>

                <div class="card-body p-4">
                    <h3 class="fw-bold mb-4 text-primary-custom" style="color: #1565C0;">
                        <?php echo __($title_key); ?>
                    </h3>

                    <!-- Section 1 -->
                    <div class="mb-4">
                        <h5 class="fw-bold text-dark mb-2"><?php echo __($h1_key); ?></h5>
                        <p class="text-muted" style="line-height: 1.6; white-space: pre-line;"><?php echo __($p1_key); ?></p>
                    </div>

                    <!-- Section 2 -->
                    <div class="mb-4">
                        <h5 class="fw-bold text-dark mb-2"><?php echo __($h2_key); ?></h5>
                        <p class="text-muted" style="line-height: 1.6; white-space: pre-line;"><?php echo __($p2_key); ?></p>
                    </div>

                    <!-- Section 3 -->
                    <div class="mb-4">
                        <h5 class="fw-bold text-dark mb-2"><?php echo __($h3_key); ?></h5>
                        <p class="text-muted" style="line-height: 1.6; white-space: pre-line;"><?php echo __($p3_key); ?></p>
                    </div>

                    <a href="<?php echo $next_url; ?>" class="btn w-100 py-2.5 text-white" style="background-color: #1565C0; border-radius: 8px;">
                        <?php echo __($btn_text_key); ?> <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>

            </div>

        </div>
    </div>
</div>

<style>
.video-thumbnail-link::after {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0; bottom: 0;
    background-color: rgba(0, 0, 0, 0.2);
    transition: background-color 0.3s ease;
}
.video-thumbnail-link:hover::after {
    background-color: rgba(0, 0, 0, 0.4);
}
.play-btn-overlay {
    transition: transform 0.2s ease;
    z-index: 10;
}
.video-thumbnail-link:hover .play-btn-overlay {
    transform: translate(-50%, -50%) scale(1.1);
}
</style>

<?php include __DIR__ . '/../views/footer.php'; ?>
