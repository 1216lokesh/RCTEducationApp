<?php
/**
 * SPA Entry Bridge
 * Serves the compiled React index.html from dist/
 */

$distIndex = __DIR__ . '/dist/index.html';

if (file_exists($distIndex)) {
    $html = file_get_contents($distIndex);
    
    // Dynamically calculate the base href relative to the domain
    $requestUri = $_SERVER['REQUEST_URI'];
    $requestPath = parse_url($requestUri, PHP_URL_PATH);
    
    // Get the directory of the current script (e.g., /rct-education-web/frontend/)
    $baseDir = dirname($requestPath);
    // Replace backslashes with forward slashes for URLs
    $baseDir = str_replace('\\', '/', $baseDir);
    if (substr($baseDir, -1) !== '/') {
        $baseDir .= '/';
    }
    
    $baseHref = $baseDir . 'dist/';
    
    // Inject the <base> tag to resolve all relative assets (CSS, JS) properly
    $baseTag = '<base href="' . htmlspecialchars($baseHref) . '">';
    
    // Insert the base tag inside <head>
    if (stripos($html, '<head>') !== false) {
        $html = preg_replace('/<head>/i', '<head>' . $baseTag, $html);
    } else {
        $html = $baseTag . $html;
    }
    
    echo $html;
} else {
    // Graceful fallback if production build hasn't run yet
    header("Content-Type: text/html; charset=utf-8");
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>RCT Education Portal - SPA Setup</title>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
        <style>
            body {
                font-family: 'Inter', system-ui, sans-serif;
                background-color: #0f172a;
                color: #f1f5f9;
                display: flex;
                align-items: center;
                justify-content: center;
                min-height: 100vh;
                margin: 0;
                padding: 1.5rem;
            }
            .card {
                background-color: #1e293b;
                border: 1px solid #334155;
                padding: 2.5rem;
                border-radius: 12px;
                max-width: 480px;
                width: 100%;
                box-shadow: 0 10px 15px -3px rgba(0,0,0,0.3);
                text-align: center;
            }
            h2 {
                color: #38bdf8;
                margin-top: 0;
                margin-bottom: 1rem;
            }
            p {
                color: #94a3b8;
                line-height: 1.6;
                margin-bottom: 1.5rem;
            }
            code {
                background-color: #0f172a;
                color: #f472b6;
                padding: 0.25rem 0.5rem;
                border-radius: 4px;
                font-family: monospace;
            }
        </style>
    </head>
    <body>
        <div class="card">
            <h2>React SPA Ready</h2>
            <p>The React migration has been successfully implemented! To run the application in production mode, compile the React assets by executing the following command in the <code>frontend</code> folder:</p>
            <p><code>npm run build</code></p>
            <p>Once compiled, refreshing this page will load the dynamic React Single-Page Application.</p>
        </div>
    </body>
    </html>
    <?php
}
?>
