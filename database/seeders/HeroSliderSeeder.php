<?php
namespace Database\Seeders;

use App\Models\HeroSlider;
use Illuminate\Database\Seeder;

class HeroSliderSeeder extends Seeder
{
    public function run(): void
    {
        $sliders = [
            ['image_path' => '/media/6767eb-hero-s26-ultra.png', 'title' => 'Samsung Galaxy S26 Ultra', 'subtitle' => 'Pre-order now', 'cta_link' => '/shop', 'cta_text' => 'Shop Now', 'sort_order' => 1, 'is_active' => true],
            ['image_path' => '/media/f69bfe-hero-turbo-fan.png', 'title' => 'Turbo Cooling Fan', 'subtitle' => 'Keep your laptop cool', 'cta_link' => '/shop/cooling-pads', 'cta_text' => 'Explore', 'sort_order' => 2, 'is_active' => true],
            ['image_path' => '/media/fab0ce-hero-ac-emi.png', 'title' => '0% EMI on All Products', 'subtitle' => '36 months installment', 'cta_link' => '/shop', 'cta_text' => 'Learn More', 'sort_order' => 3, 'is_active' => true],
        ];

        foreach ($sliders as $s) {
            HeroSlider::create($s);
        }
    }
}
