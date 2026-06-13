<?php
require_once __DIR__ . '/../backend/includes/init.php';

if ($auth->isLoggedIn()) {
    $user = $auth->getCurrentUser();
    if ($user['role'] === 'patient') {
        header('Location: ' . APP_URL . '/patient/dashboard.php');
    } else {
        header('Location: ' . APP_URL . '/admin/dashboard.php');
    }
    exit;
}
?>

<?php include __DIR__ . '/views/header.php'; ?>

<?php
// Show multilingual splash screen once for anonymous visitors
if (!isset($_GET['skip_splash']) && empty($_COOKIE['rct_splash_shown']) && !$auth->isLoggedIn()) {
    include __DIR__ . '/views/splash.php';
    exit;
}
?>

<div class="container-fluid py-5">
    <div class="row">
        <div class="col-md-8">
            <h1 class="display-4 mb-4"><?php echo __('welcome'); ?> to <?php echo __('app_name'); ?></h1>
            <p class="lead">
                <?php echo __('app_description') ?? 'A comprehensive education portal for RCT (Root Canal Treatment) patients'; ?>
            </p>
            
            <div class="mt-5">
                <h3>Features</h3>
                <ul class="list-group">
                    <li class="list-group-item">
                        <i class="fas fa-check-circle text-success"></i> Interactive education modules
                    </li>
                    <li class="list-group-item">
                        <i class="fas fa-check-circle text-success"></i> Multi-language support (English, Tamil, Hindi, Telugu)
                    </li>
                    <li class="list-group-item">
                        <i class="fas fa-check-circle text-success"></i> Progress tracking with assessments
                    </li>
                    <li class="list-group-item">
                        <i class="fas fa-check-circle text-success"></i> Personalized treatment journey
                    </li>
                    <li class="list-group-item">
                        <i class="fas fa-check-circle text-success"></i> Digital consent forms
                    </li>
                    <li class="list-group-item">
                        <i class="fas fa-check-circle text-success"></i> Admin dashboard for dentists
                    </li>
                </ul>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card shadow-lg">
                <div class="card-body p-4">
                    <h4 class="card-title text-center mb-4">Get Started</h4>
                    
                    <a href="<?php echo APP_URL; ?>/auth/login.php" class="btn btn-primary w-100 mb-3">
                        <i class="fas fa-sign-in-alt"></i> <?php echo __('login'); ?>
                    </a>
                    
                    <a href="<?php echo APP_URL; ?>/auth/register.php" class="btn btn-success w-100">
                        <i class="fas fa-user-plus"></i> <?php echo __('register'); ?>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/views/footer.php'; ?>