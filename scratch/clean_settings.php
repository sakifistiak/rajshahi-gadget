<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\SiteSetting;

SiteSetting::updateOrCreate(
    ['key' => 'site_name'],
    ['value' => 'Khan Gadget']
);

SiteSetting::updateOrCreate(
    ['key' => 'site_slogan'],
    ['value' => 'Brand NEW Intact BOX, Without BOX & Pre-Owned']
);

echo "Settings updated clean!\n";
