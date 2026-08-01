<?php

$dir = __DIR__ . '/../resources/views/pages';
$files = glob($dir . '/*.blade.php');
$files = array_merge($files, glob($dir . '/**/*.blade.php'));

foreach ($files as $file) {
    if (!is_file($file)) continue;
    $content = file_get_contents($file);

    // Remove compare button elements (button or a tag with aria-label="Add to compare" or title="Compare")
    $content = preg_replace('/<button\b[^>]*aria-label="Add to compare"[^>]*>.*?<\/button>/is', '', $content);
    $content = preg_replace('/<button\b[^>]*title="Compare"[^>]*>.*?<\/button>/is', '', $content);
    $content = preg_replace('/<a\b[^>]*aria-label="Add to compare"[^>]*>.*?<\/a>/is', '', $content);
    $content = preg_replace('/<a\b[^>]*title="Compare"[^>]*>.*?<\/a>/is', '', $content);
    
    // Also remove wishlist hover buttons if requested ("হোভার করলে কিছুই দেখানোর দরকার নাই")
    $content = preg_replace('/<button\b[^>]*aria-label="Save to wishlist"[^>]*>.*?<\/button>/is', '', $content);

    file_put_contents($file, $content);
}

echo "Successfully removed compare and wishlist hover buttons across all product cards!\n";
