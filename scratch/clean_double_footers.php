<?php

$dir = __DIR__ . '/../resources/views/pages';
$files = glob($dir . '/*.blade.php');
$files = array_merge($files, glob($dir . '/**/*.blade.php'));

foreach ($files as $file) {
    if (!is_file($file)) continue;
    $content = file_get_contents($file);

    // 1. Remove raw <footer ... </footer> if present
    $content = preg_replace('/<footer\b[^>]*>.*?<\/footer>/s', '', $content);

    // 2. Count @include('partials.footer')
    $count = substr_count($content, "@include('partials.footer')");
    if ($count > 1) {
        // Keep only the first occurrence
        $firstPos = strpos($content, "@include('partials.footer')");
        $content = substr($content, 0, $firstPos + strlen("@include('partials.footer')"))
            . str_replace("@include('partials.footer')", '', substr($content, $firstPos + strlen("@include('partials.footer')")));
    } elseif ($count === 0) {
        // Insert right before </div></body> or </div>@include('partials.mobile-drawer') or at bottom of main
        if (strpos($content, "@include('partials.mobile-drawer')") !== false) {
            $content = str_replace("@include('partials.mobile-drawer')", "@include('partials.footer')\n@include('partials.mobile-drawer')", $content);
        } elseif (strpos($content, '</body>') !== false) {
            $content = str_replace('</body>', "@include('partials.footer')\n</body>", $content);
        } else {
            $content .= "\n@include('partials.footer')";
        }
    }

    file_put_contents($file, $content);
}
echo "Cleaned up double footers across all views!\n";
