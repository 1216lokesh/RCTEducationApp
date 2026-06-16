    </main>
    
    <footer class="bg-dark text-white mt-5 py-4">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-4">
                    <h5><?php echo __('app_name'); ?></h5>
                    <p><?php echo __('about'); ?></p>
                </div>
                <div class="col-md-4">
                    <h5><?php echo __('help'); ?></h5>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-white-50"><?php echo __('about'); ?></a></li>
                        <li><a href="#" class="text-white-50"><?php echo __('help'); ?></a></li>
                        <li><a href="#" class="text-white-50">Contact Us</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h5><?php echo __('language'); ?></h5>
                    <ul class="list-unstyled">
                        <li><a href="?lang=en" class="text-white-50">English</a></li>
                        <li><a href="?lang=ta" class="text-white-50">Tamil</a></li>
                        <li><a href="?lang=hi" class="text-white-50">Hindi</a></li>
                        <li><a href="?lang=te" class="text-white-50">Telugu</a></li>
                    </ul>
                </div>
            </div>
            <hr class="bg-white-50">
            <div class="text-center">
                <p>&copy; <?php echo date('Y'); ?> <?php echo __('app_name'); ?>. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS from CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- jQuery (Optional) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Custom JS -->
    <script src="<?php echo APP_URL; ?>/assets/js/script.js"></script>
</body>
</html>
