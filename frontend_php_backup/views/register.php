<?php
// Register form view — expects `$errors` and `$formData` variables set by caller
?>
<?php include __DIR__ . '/header.php'; ?>

<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow">
                <div class="card-body p-5">
                    <h2 class="text-center mb-4">
                        <i class="fas fa-user-plus"></i> <?php echo __('register'); ?>
                    </h2>

                    <?php if (!empty($errors['general'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($errors['general']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php endif; ?>

                    <form method="POST" action="" novalidate autocomplete="off">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="first_name" class="form-label"><?php echo __('first_name'); ?></label>
                                <input type="text" class="form-control <?php echo isset($errors['first_name']) ? 'is-invalid' : ''; ?>" 
                                       id="first_name" name="first_name" value="<?php echo htmlspecialchars($formData['first_name']); ?>" required autocomplete="off">
                                <?php if (isset($errors['first_name'])): ?>
                                <div class="invalid-feedback"><?php echo $errors['first_name']; ?></div>
                                <?php endif; ?>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="last_name" class="form-label"><?php echo __('last_name'); ?></label>
                                <input type="text" class="form-control <?php echo isset($errors['last_name']) ? 'is-invalid' : ''; ?>" 
                                       id="last_name" name="last_name" value="<?php echo htmlspecialchars($formData['last_name']); ?>" required autocomplete="off">
                                <?php if (isset($errors['last_name'])): ?>
                                <div class="invalid-feedback"><?php echo $errors['last_name']; ?></div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label"><?php echo __('email'); ?></label>
                            <input type="email" class="form-control <?php echo isset($errors['email']) ? 'is-invalid' : ''; ?>" 
                                   id="email" name="email" value="<?php echo htmlspecialchars($formData['email']); ?>" required autocomplete="off">
                            <?php if (isset($errors['email'])): ?>
                            <div class="invalid-feedback"><?php echo $errors['email']; ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <label for="phone" class="form-label"><?php echo __('phone'); ?></label>
                            <input type="tel" class="form-control" id="phone" name="phone" 
                                   value="<?php echo htmlspecialchars($formData['phone']); ?>">
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="date_of_birth" class="form-label"><?php echo __('date_of_birth'); ?></label>
                                <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" 
                                       value="<?php echo htmlspecialchars($formData['date_of_birth']); ?>">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="gender" class="form-label"><?php echo __('gender'); ?></label>
                                <select class="form-select" id="gender" name="gender">
                                    <option value=""><?php echo __('select'); ?> <?php echo __('gender'); ?></option>
                                    <option value="M" <?php echo $formData['gender'] === 'M' ? 'selected' : ''; ?>><?php echo __('male'); ?></option>
                                    <option value="F" <?php echo $formData['gender'] === 'F' ? 'selected' : ''; ?>><?php echo __('female'); ?></option>
                                    <option value="Other" <?php echo $formData['gender'] === 'Other' ? 'selected' : ''; ?>><?php echo __('other'); ?></option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="language" class="form-label"><?php echo __('language'); ?></label>
                            <select class="form-select" id="language" name="language">
                                <option value="en" <?php echo $formData['language'] === 'en' ? 'selected' : ''; ?>>English</option>
                                <option value="ta" <?php echo $formData['language'] === 'ta' ? 'selected' : ''; ?>>Tamil (தமிழ்)</option>
                                <option value="hi" <?php echo $formData['language'] === 'hi' ? 'selected' : ''; ?>>Hindi (हिंदी)</option>
                                <option value="te" <?php echo $formData['language'] === 'te' ? 'selected' : ''; ?>>Telugu (తెలుగు)</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label"><?php echo __('password'); ?></label>
                            <input type="password" class="form-control <?php echo isset($errors['password']) ? 'is-invalid' : ''; ?>" 
                                   id="password" name="password" required autocomplete="new-password">
                            <?php if (isset($errors['password'])): ?>
                            <div class="invalid-feedback"><?php echo $errors['password']; ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <label for="confirm_password" class="form-label"><?php echo __('confirm_password'); ?></label>
                            <input type="password" class="form-control <?php echo isset($errors['confirm_password']) ? 'is-invalid' : ''; ?>" 
                                   id="confirm_password" name="confirm_password" required autocomplete="new-password">
                            <?php if (isset($errors['confirm_password'])): ?>
                            <div class="invalid-feedback"><?php echo $errors['confirm_password']; ?></div>
                            <?php endif; ?>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 mb-3">
                            <i class="fas fa-user-plus"></i> <?php echo __('register'); ?>
                        </button>
                    </form>

                    <hr>

                    <p class="text-center">
                        <?php echo __('already_have_account') ?? 'Already have an account?'; ?>
                        <a href="<?php echo APP_URL; ?>/auth/login.php"><?php echo __('login'); ?></a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/footer.php'; ?>
