<?php
// add_ads.php
$dirs = [
    __DIR__ . '/pages',
    __DIR__ . '/clubs',
    __DIR__ . '/guess',
    __DIR__ . '/hockeygrid',
    __DIR__ . '/wordle',
    __DIR__ . '/paginas_errores'
];

foreach ($dirs as $dir) {
    if (!is_dir($dir)) continue;

    $files = scandir($dir);
    foreach ($files as $file) {
        if (pathinfo($file, PATHINFO_EXTENSION) === 'php') {
            $path = $dir . '/' . $file;
            $content = file_get_contents($path);
            
            // Skip if already contains ads.php
            if (strpos($content, 'includes/ads.php') !== false) {
                continue;
            }

            // Figure out the path to includes
            // From pages/ it is '../includes/ads.php'
            // From clubs/ it is '../includes/ads.php'
            $includeLine = "<?php include __DIR__ . '/../includes/ads.php'; ?>";
            
            // Inject right after <head>
            if (strpos($content, '<head>') !== false) {
                // Determine indentation
                $replacement = "<head>\n    " . $includeLine;
                $newContent = preg_replace('/<head>/i', $replacement, $content, 1);
                file_put_contents($path, $newContent);
                echo "Added ads to $path\n";
            }
        }
    }
}
echo "Done.\n";
