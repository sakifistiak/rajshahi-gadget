<?php

$dir = __DIR__ . '/../resources/views/pages';
$files = glob($dir . '/*.blade.php');
$files = array_merge($files, glob($dir . '/**/*.blade.php'));

foreach ($files as $file) {
    if (!is_file($file)) continue;
    $content = file_get_contents($file);
    if (strpos($content, '<footer') !== false) {
        echo "Updating footer in: " . basename($file) . "\n";
        $newContent = preg_replace('/<footer\b[^>]*>.*?<\/footer>/s', "@include('partials.footer')", $content);
        file_put_contents($file, $newContent);
    }
}
echo "All done!\n";
