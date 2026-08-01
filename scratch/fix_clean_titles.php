<?php

$dir = __DIR__ . '/../resources/views/pages';
$files = glob($dir . '/*.blade.php');
$files = array_merge($files, glob($dir . '/**/*.blade.php'));

$titlesMap = [
    'home' => "{{ \$siteName ?? 'Khan Gadget' }} — {{ \$siteSlogan ?? 'Brand NEW Intact BOX, Without BOX & Pre-Owned' }}",
    'about' => "About — {{ \$siteName ?? 'Khan Gadget' }}",
    'account' => "Account — {{ \$siteName ?? 'Khan Gadget' }}",
    'admin' => "Admin — {{ \$siteName ?? 'Khan Gadget' }}",
    'blog' => "Blog — {{ \$siteName ?? 'Khan Gadget' }}",
    'cart' => "Shopping Cart — {{ \$siteName ?? 'Khan Gadget' }}",
    'checkout' => "Checkout — {{ \$siteName ?? 'Khan Gadget' }}",
    'compare' => "Compare Products — {{ \$siteName ?? 'Khan Gadget' }}",
    'contact' => "Contact Us — {{ \$siteName ?? 'Khan Gadget' }}",
    'customer-feedback' => "Customer Feedback — {{ \$siteName ?? 'Khan Gadget' }}",
    'customer-spotlight' => "Customer Spotlight — {{ \$siteName ?? 'Khan Gadget' }}",
    'philanthropic-work' => "Philanthropic Work — {{ \$siteName ?? 'Khan Gadget' }}",
    'shop' => "Shop — {{ \$siteName ?? 'Khan Gadget' }}",
    'detail' => "{{ \$product->title ?? 'Product Detail' }} — {{ \$siteName ?? 'Khan Gadget' }}",
];

foreach ($files as $file) {
    if (!is_file($file)) continue;
    $content = file_get_contents($file);
    $basename = basename($file, '.blade.php');

    $titleText = $titlesMap[$basename] ?? (ucwords(str_replace('-', ' ', $basename)) . " — {{ \$siteName ?? 'Khan Gadget' }}");

    // Replace <title>...</title>
    $content = preg_replace('/<title>.*?<\/title>/is', "<title>{$titleText}</title>", $content);

    // Replace og:title
    $content = preg_replace('/<meta\s+property="og:title"\s+content="[^"]*"\s*\/?>/i', "<meta property=\"og:title\" content=\"{$titleText}\"/>", $content);

    // Replace twitter:title
    $content = preg_replace('/<meta\s+name="twitter:title"\s+content="[^"]*"\s*\/?>/i', "<meta name=\"twitter:title\" content=\"{$titleText}\"/>", $content);

    // Replace author & twitter:site
    $content = preg_replace('/<meta\s+name="author"\s+content="[^"]*"\s*\/?>/i', '<meta name="author" content="{{ $siteName ?? \'Khan Gadget\' }}"/>', $content);
    $content = preg_replace('/<meta\s+name="twitter:site"\s+content="[^"]*"\s*\/?>/i', '<meta name="twitter:site" content="{{ $siteName ?? \'Khan Gadget\' }}"/>', $content);

    file_put_contents($file, $content);
}

echo "Cleaned up all page title and meta tags with 100% clean UTF-8 Blade syntax!\n";
