<?php
namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['slug' => 'laptops', 'name' => 'Laptops', 'tagline' => 'Every budget, every brand', 'image' => '/assets/laptop-ultrabook-C5nU_6_f.jpg', 'item_count' => 148, 'sort_order' => 1],
            ['slug' => 'gaming-laptops', 'name' => 'Gaming Laptops', 'tagline' => 'Frames that matter', 'image' => '/assets/laptop-gaming-CRMi9E0Q.jpg', 'item_count' => 42, 'sort_order' => 2],
            ['slug' => 'bags', 'name' => 'Laptop Bags', 'tagline' => 'Carry it safe', 'image' => '/assets/acc-bag-DUSyzYyb.jpg', 'item_count' => 26, 'sort_order' => 3],
            ['slug' => 'mouse', 'name' => 'Mouse', 'tagline' => 'Wired & wireless', 'image' => '/assets/acc-mouse-BbNzBi1z.jpg', 'item_count' => 34, 'sort_order' => 4],
            ['slug' => 'keyboard', 'name' => 'Keyboards', 'tagline' => 'Type better', 'image' => '/assets/acc-keyboard-C2O0-ufI.jpg', 'item_count' => 28, 'sort_order' => 5],
            ['slug' => 'ram', 'name' => 'RAM', 'tagline' => 'Instant upgrade', 'image' => '/assets/acc-ram-bHl5zo9i.jpg', 'item_count' => 22, 'sort_order' => 6],
            ['slug' => 'headphones', 'name' => 'Headphones', 'tagline' => 'Work & play', 'image' => '/assets/acc-headphone-B-geqvKK.jpg', 'item_count' => 31, 'sort_order' => 7],
            ['slug' => 'mousepad', 'name' => 'Mousepads', 'tagline' => 'Desk essentials', 'image' => '/assets/acc-mousepad-BqJuH98_.jpg', 'item_count' => 18, 'sort_order' => 8],
            ['slug' => 'ssd', 'name' => 'SSD & Storage', 'tagline' => 'NVMe & SATA upgrades', 'image' => '/assets/acc-ram-bHl5zo9i.jpg', 'item_count' => 19, 'sort_order' => 9],
            ['slug' => 'chargers', 'name' => 'Chargers & Adapters', 'tagline' => 'Original & compatible', 'image' => '/assets/acc-bag-DUSyzYyb.jpg', 'item_count' => 24, 'sort_order' => 10],
            ['slug' => 'batteries', 'name' => 'Laptop Batteries', 'tagline' => 'Fresh cells, real backup', 'image' => '/assets/laptop-business-3Mb6HsAh.jpg', 'item_count' => 16, 'sort_order' => 11],
            ['slug' => 'cooling-pads', 'name' => 'Cooling Pads', 'tagline' => 'Keep the thermals sane', 'image' => '/assets/acc-mousepad-BqJuH98_.jpg', 'item_count' => 12, 'sort_order' => 12],
            ['slug' => 'laptop-stands', 'name' => 'Laptop Stands', 'tagline' => 'Better posture, better airflow', 'image' => '/assets/acc-bag-DUSyzYyb.jpg', 'item_count' => 14, 'sort_order' => 13],
            ['slug' => 'webcams', 'name' => 'Webcams', 'tagline' => 'Meetings that look sharp', 'image' => '/assets/acc-headphone-B-geqvKK.jpg', 'item_count' => 11, 'sort_order' => 14],
            ['slug' => 'hubs', 'name' => 'Hubs & Docks', 'tagline' => 'USB‑C, HDMI, card readers', 'image' => '/assets/acc-keyboard-C2O0-ufI.jpg', 'item_count' => 17, 'sort_order' => 15],
        ];

        foreach ($categories as $cat) {
            Category::create($cat);
        }
    }
}
