<?php

namespace Database\Seeders;

use App\Models\PromoBanner;
use Illuminate\Database\Seeder;

class PromoBannerSeeder extends Seeder
{
    public function run(): void
    {
        PromoBanner::create([
            'title' => 'Up to ৳ 9,500 OFF',
            'subtitle' => 'on Apple Watch Series 10 · Ends 25 July',
            'image_path' => '/assets/cat-wearables-WxrTS6bM.jpg',
            'bg_color' => 'from-sky-100 to-sky-50',
            'link' => '/shop?condition=intact',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        PromoBanner::create([
            'title' => 'Special Price ৳ 1,499',
            'subtitle' => 'AGX HF40 Portable Fan · 6 Month Warranty · 5hr backup',
            'image_path' => '/assets/cat-audio-TSO8_LKU.jpg',
            'bg_color' => 'from-orange-200 to-orange-100',
            'link' => '/shop?condition=without-box',
            'sort_order' => 2,
            'is_active' => true,
        ]);
    }
}
