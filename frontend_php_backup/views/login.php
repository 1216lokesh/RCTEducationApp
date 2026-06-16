<?php
// Login form view — expects `$error` and `$email` variables to be set by caller
?>
<?php include __DIR__ . '/header.php'; ?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow">
                <div class="card-body p-5">
                    <h2 class="text-center mb-4">
                        <i class="fas fa-sign-in-alt"></i> <?php echo __('login'); ?>
                    </h2>

                    <?php if (!empty($error)): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php endif; ?>

                    <form method="POST" action="" novalidate autocomplete="off">
                        <div class="mb-3">
                            <label for="email" class="form-label"><?php echo __('email'); ?></label>
                            <input type="email" class="form-control" id="email" name="email" 
                                   value="<?php echo htmlspecialchars($email ?? ''); ?>" required autocomplete="off">
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label"><?php echo __('password'); ?></label>
                            <input type="password" class="form-control" id="password" name="password" required autocomplete="new-password">
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="remember_me" 
                                   name="remember_me" value="1">
                            <label class="form-check-label" for="remember_me">
                                <?php echo __('remember_me'); ?>
                            </label>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 mb-3">
                            <i class="fas fa-sign-in-alt"></i> <?php echo __('login'); ?>
                        </button>
                    </form>

                    <hr>

                    <p class="text-center">
                        <?php echo __('register_prompt') ?? "Don't have an account?"; ?>
                        <a href="<?php echo APP_URL; ?>/auth/register.php"><?php echo __('register'); ?></a>
                    </p>
                    <div class="mt-4">
                        <label class="form-label"><?php echo __('language'); ?></label>
                        <div class="btn-group w-100" role="group">
                            <a href="?lang=en" class="btn btn-sm btn-outline-secondary <?php echo Language::current() === 'en' ? 'active' : ''; ?>">EN</a>
                            <a href="?lang=ta" class="btn btn-sm btn-outline-secondary <?php echo Language::current() === 'ta' ? 'active' : ''; ?>">TA</a>
                            <a href="?lang=hi" class="btn btn-sm btn-outline-secondary <?php echo Language::current() === 'hi' ? 'active' : ''; ?>">HI</a>
                            <a href="?lang=te" class="btn btn-sm btn-outline-secondary <?php echo Language::current() === 'te' ? 'active' : ''; ?>">TE</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/footer.php'; ?>
