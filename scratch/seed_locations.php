<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\StoreLocation;

$locations = [
    ['name' => 'Dhanmondi', 'address' => 'House 12, Road 5, Dhanmondi, Dhaka 1205', 'phone' => '+8801700000001', 'sort_order' => 1],
    ['name' => 'Uttara', 'address' => 'Sector 7, Rabindra Sarani, Uttara, Dhaka 1230', 'phone' => '+8801700000002', 'sort_order' => 2],
    ['name' => 'Mirpur', 'address' => 'Plot 9, Mirpur 10 Circle, Dhaka 1216', 'phone' => '+8801700000003', 'sort_order' => 3],
    ['name' => 'Gulshan', 'address' => 'Road 11, Gulshan 1, Dhaka 1212', 'phone' => '+8801700000004', 'sort_order' => 4],
    ['name' => 'Bashundhara City', 'address' => 'Level 6, Bashundhara City, Panthapath, Dhaka', 'phone' => '+8801700000005', 'sort_order' => 5],
    ['name' => 'Motijheel', 'address' => 'Dilkusha C/A, Motijheel, Dhaka 1000', 'phone' => '+8801700000006', 'sort_order' => 6],
    ['name' => 'Chattogram', 'address' => 'GEC Circle, Nasirabad, Chattogram 4000', 'phone' => '+8801700000007', 'sort_order' => 7],
    ['name' => 'Sylhet', 'address' => 'Zindabazar Main Road, Sylhet 3100', 'phone' => '+8801700000008', 'sort_order' => 8],
    ['name' => 'Khulna', 'address' => 'Shib Bari More, Khulna 9100', 'phone' => '+8801700000009', 'sort_order' => 9],
    ['name' => 'Rajshahi', 'address' => 'Shaheb Bazar Zero Point, Rajshahi 6100', 'phone' => '+8801700000010', 'sort_order' => 10],
    ['name' => 'Cumilla', 'address' => 'Kandirpar, Cumilla 3500', 'phone' => '+8801700000011', 'sort_order' => 11],
    ['name' => 'Rangpur', 'address' => 'Jahaj Company More, Rangpur 5400', 'phone' => '+8801700000012', 'sort_order' => 12],
];

foreach ($locations as $loc) {
    StoreLocation::firstOrCreate(
        ['name' => $loc['name']],
        $loc
    );
}

echo "Successfully seeded " . count($locations) . " store locations!\n";
