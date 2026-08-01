<?php

$dir = __DIR__ . '/../resources/views/pages';
$files = glob($dir . '/*.blade.php');
$files = array_merge($files, glob($dir . '/**/*.blade.php'));

foreach ($files as $file) {
    if (!is_file($file)) continue;
    $content = file_get_contents($file);

    $basename = basename($file, '.blade.php');
    $isHome = ($basename === 'home');

    // 1. Title tag replacement
    if ($isHome) {
        $titleReplacement = "<title>{{ \$siteName ?? 'Khan Gadget' }} — {{ \$siteSlogan ?? 'Brand NEW Intact BOX, Without BOX & Pre-Owned' }}</title>";
    } else {
        // Extract existing title if present e.g. <title>About — Nova</title>
        if (preg_match('/<title>(.*?)<\/title>/i', $content, $m)) {
            $existingTitle = trim($m[1]);
            // Remove old brand suffix like " — Nova" or " — Khan Gadget"
            $pageName = preg_replace('/\s*[\—\-]\s*(Nova|Khan Gadget.*)/i', '', $existingTitle);
            $pageName = htmlspecialchars_decode($pageName, ENT_QUOTES);
            if (empty($pageName)) {
                $pageName = ucfirst($basename);
            }
            $titleReplacement = "<title>{$pageName} — {{ \$siteName ?? 'Khan Gadget' }}</title>";
        } else {
            $pageName = ucfirst($basename);
            $titleReplacement = "<title>{$pageName} — {{ \$siteName ?? 'Khan Gadget' }}</title>";
        }
    }

    $content = preg_replace('/<title>.*?<\/title>/is', $titleReplacement, $content);

    // 2. Meta Author & Twitter site replacement
    $content = preg_replace('/<meta\s+name="author"\s+content="[^"]*"\s*\/?>/i', '<meta name="author" content="{{ $siteName ?? \'Khan Gadget\' }}"/>', $content);
    $content = preg_replace('/<meta\s+name="twitter:site"\content="[^"]*"\s*\/?>/i', '<meta name="twitter:site" content="{{ $siteName ?? \'Khan Gadget\' }}"/>', $content);

    // 3. Meta Twitter Title & OG Title replacement
    if ($isHome) {
        $content = preg_replace('/<meta\s+name="twitter:title"\s+content="[^"]*"\s*\/?>/i', '<meta name="twitter:title" content="{{ $siteName ?? \'Khan Gadget\' }} — {{ $siteSlogan ?? \'Brand NEW Intact BOX, Without BOX & Pre-Owned\' }}"/>', $content);
        $content = preg_replace('/<meta\s+property="og:title"\s+content="[^"]*"\s*\/?>/i', '<meta property="og:title" content="{{ $siteName ?? \'Khan Gadget\' }} — {{ $siteSlogan ?? \'Brand NEW Intact BOX, Without BOX & Pre-Owned\' }}"/>', $content);
    } else {
        $content = preg_replace('/<meta\s+name="twitter:title"\s+content="[^"]*"\s*\/?>/i', '<meta name="twitter:title" content="{{ $siteName ?? \'Khan Gadget\' }}"/>', $content);
        $content = preg_replace('/<meta\s+property="og:title"\s+content="[^"]*"\s*\/?>/i', '<meta property="og:title" content="{{ $siteName ?? \'Khan Gadget\' }}"/>', $content);
    }

    // 4. Meta Description & OG Description & Twitter Description
    $content = preg_replace('/<meta\s+name="description"\s+content="[^"]*"\s*\/?>/i', '<meta name="description" content="{{ $siteDescription ?? \'Bangladesh-er trusted destination for Brand new intact box, without box and certified pre-owned gadgets.\' }}"/>', $content);
    $content = preg_replace('/<meta\s+property="og:description"\s+content="[^"]*"\s*\/?>/i', '<meta property="og:description" content="{{ $siteDescription ?? \'Bangladesh-er trusted destination for Brand new intact box, without box and certified pre-owned gadgets.\' }}"/>', $content);
    $content = preg_replace('/<meta\s+name="twitter:description"\s+content="[^"]*"\s*\/?>/i', '<meta name="twitter:description" content="{{ $siteDescription ?? \'Bangladesh-er trusted destination for Brand new intact box, without box and certified pre-owned gadgets.\' }}"/>', $content);

    file_put_contents($file, $content);
}

echo "Successfully updated title and meta tags across all Blade pages!\n";
