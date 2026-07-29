<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            BrandSeeder::class,
            CategorySeeder::class,
            ConditionSeeder::class,
            ProductSeeder::class,
            BlogPostSeeder::class,
            TestimonialSeeder::class,
            CustomerFeedbackSeeder::class,
            CustomerSpotlightSeeder::class,
            PhilanthropicWorkSeeder::class,
            HeroSliderSeeder::class,
            SiteSettingSeeder::class,
        ]);
    }
}
