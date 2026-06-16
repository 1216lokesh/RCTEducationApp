<?php include __DIR__ . '/header.php'; ?>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            
            <!-- Welcome Header -->
            <div class="text-start mb-4">
                <h2 class="fw-bold mb-1 text-primary-custom" style="color: #1565C0;"><?php echo __('welcome'); ?>, <?php echo htmlspecialchars($user['name']); ?></h2>
                <p class="text-muted"><?php echo __('dashboard_subtitle') ?? 'Select your appointment below'; ?></p>
            </div>

            <!-- Procedure Card -->
            <div class="card mb-4 border-0 rounded-3 shadow-sm" style="background-color: #E3F2FD; border-left: 5px solid #1565C0 !important;">
                <div class="card-body p-4">
                    <?php if ($procedure): ?>
                        <h5 class="fw-bold mb-1" style="color: #1565C0;"><i class="fas fa-file-invoice"></i> <?php echo __('procedure_title') ?? 'Procedure'; ?>: <?php echo htmlspecialchars($procedure['procedure_name']); ?></h5>
                        <p class="mb-2 text-dark font-monospace" style="font-size: 0.85rem;"><i class="fas fa-clinic-medical"></i> <?php echo __('procedure_category') ?? 'Category'; ?>: <?php echo htmlspecialchars($procedure['category']); ?></p>
                        <p class="mb-0 text-muted" style="font-size: 0.9rem;"><?php echo htmlspecialchars($procedure['description']); ?></p>
                        <hr class="my-2 border-primary opacity-25">
                        <p class="mb-0 fw-bold" style="color: #0288D1; font-size: 0.85rem;">
                            <i class="fas fa-users"></i> <?php echo __('procedure_group') ?? 'Your Group'; ?>: 
                            <?php echo strcasecmp($procedure['group_type'], 'Intervention') === 0 ? (__('procedure_intervention') ?? 'Intervention Group') : (__('procedure_comparator') ?? 'Standard Care Group'); ?>
                        </p>
                    <?php else: ?>
                        <h5 class="fw-bold mb-1" style="color: #D32F2F;"><i class="fas fa-exclamation-triangle"></i> <?php echo __('procedure_not_assigned') ?? 'No procedure assigned yet'; ?></h5>
                        <p class="mb-0 text-muted" style="font-size: 0.9rem;">Please contact your dentist to assign your procedure.</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Progress Card -->
            <div class="card mb-4 border-0 rounded-3 shadow-sm" style="background-color: #F0F4FF;">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="fw-bold mb-0 text-primary-custom" style="color: #1565C0;">Your Progress</h6>
                        <span class="fw-bold" style="color: #1565C0; font-size: 0.9rem;"><?php echo $progress; ?> / 4 Completed</span>
                    </div>
                    <div class="progress rounded-pill" style="height: 12px; background-color: #C5CAE9;">
                        <div class="progress-bar rounded-pill" role="progressbar" style="width: <?php echo ($progress * 25); ?>%; background-color: #1565C0;" aria-valuenow="<?php echo $progress; ?>" aria-valuemin="0" aria-valuemax="4"></div>
                    </div>
                </div>
            </div>

            <!-- Journey Stepper / Buttons -->
            <div class="d-flex flex-column gap-3 mb-4">
                
                <!-- Appointment 1 -->
                <div>
                    <span class="text-muted d-block mb-1" style="font-size: 0.8rem; font-weight: 500;"><?php echo __('apt1_label') ?? 'Appointment 1 — Diagnosis'; ?></span>
                    <a href="<?php echo APP_URL; ?>/patient/baseline.php?apt=1" 
                       class="btn w-100 py-2.5 text-white d-flex align-items-center justify-content-between px-3 <?php echo $apt1_completed ? 'disabled' : ''; ?>" 
                       style="background-color: #1565C0; border-radius: 8px;">
                        <span><?php echo __('appointment1') ?? 'Start Appointment 1'; ?></span>
                        <i class="fas <?php echo $apt1_completed ? 'fa-check-circle' : 'fa-arrow-right'; ?>"></i>
                    </a>
                </div>

                <!-- Appointment 2 -->
                <div>
                    <span class="text-muted d-block mb-1" style="font-size: 0.8rem; font-weight: 500;"><?php echo __('apt2_label') ?? 'Appointment 2 — Root Canal Procedure'; ?></span>
                    <a href="<?php echo APP_URL; ?>/patient/baseline.php?apt=2" 
                       class="btn w-100 py-2.5 text-white d-flex align-items-center justify-content-between px-3 <?php echo ($apt2_completed || !$apt1_completed) ? 'disabled' : ''; ?>" 
                       style="background-color: #1565C0; border-radius: 8px;">
                        <span><?php echo __('appointment2') ?? 'Start Appointment 2'; ?></span>
                        <i class="fas <?php echo $apt2_completed ? 'fa-check-circle' : 'fa-arrow-right'; ?>"></i>
                    </a>
                </div>

                <!-- Appointment 3 -->
                <div>
                    <span class="text-muted d-block mb-1" style="font-size: 0.8rem; font-weight: 500;"><?php echo __('apt3_label') ?? 'Appointment 3 — Crown / Final Restoration'; ?></span>
                    <a href="<?php echo APP_URL; ?>/patient/baseline.php?apt=3" 
                       class="btn w-100 py-2.5 text-white d-flex align-items-center justify-content-between px-3 <?php echo ($apt3_completed || !$apt2_completed) ? 'disabled' : ''; ?>" 
                       style="background-color: #1565C0; border-radius: 8px;">
                        <span><?php echo __('appointment3') ?? 'Start Appointment 3'; ?></span>
                        <i class="fas <?php echo $apt3_completed ? 'fa-check-circle' : 'fa-arrow-right'; ?>"></i>
                    </a>
                </div>

                <!-- Follow Up -->
                <div>
                    <span class="text-muted d-block mb-1" style="font-size: 0.8rem; font-weight: 500;"><?php echo __('followup_label') ?? 'Follow Up Visit'; ?></span>
                    <a href="<?php echo APP_URL; ?>/patient/baseline.php?apt=4" 
                       class="btn w-100 py-2.5 text-white d-flex align-items-center justify-content-between px-3 <?php echo ($followup_completed || !$apt3_completed) ? 'disabled' : ''; ?>" 
                       style="background-color: #0288D1; border-radius: 8px;">
                        <span><?php echo __('followup') ?? 'Start Follow Up'; ?></span>
                        <i class="fas <?php echo $followup_completed ? 'fa-check-circle' : 'fa-arrow-right'; ?>"></i>
                    </a>
                </div>

            </div>

            <!-- Logout Button -->
            <a href="<?php echo APP_URL; ?>/auth/logout.php" class="btn w-100 py-2.5 text-white shadow-sm" style="background-color: #D32F2F; border-radius: 8px;">
                <i class="fas fa-sign-out-alt"></i> <?php echo __('logout'); ?>
            </a>

        </div>
    </div>
</div>

<?php include __DIR__ . '/footer.php'; ?>
