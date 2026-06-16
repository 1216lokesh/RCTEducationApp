<?php
// Standalone multilingual splash screen
?>
<!DOCTYPE html>
<html lang="<?php echo \Language::current(); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo __('app_name'); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo APP_URL; ?>/assets/css/style.css">
    <style>
        .splash-screen{
            height:100vh;
            display:flex;
            align-items:center;
            justify-content:center;
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            color:#fff;
        }
        .splash-card{background:transparent;border:none}
    </style>
</head>
<body>
    <div class="splash-screen">
        <div class="text-center">
            <h1 class="display-4 mb-3"><?php echo __('splash_title'); ?></h1>
            <p class="lead opacity-75"><?php echo __('splash_subtitle'); ?></p>
            <div class="mt-4">
                <div class="loading"></div>
            </div>
        </div>
    </div>

    <script>
        // mark splash shown and redirect to main page
        document.cookie = "rct_splash_shown=1; path=/";
        setTimeout(function(){ window.location = "<?php echo APP_URL; ?>/index.php?skip_splash=1"; }, 2200);
    </script>
</body>
</html>
