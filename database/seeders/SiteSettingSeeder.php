<?php
namespace Database\Seeders;

use App\Models\SiteSetting;
use Illuminate\Database\Seeder;

class SiteSettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            'phone' => '+8801700000000',
            'whatsapp' => '8801700000000',
            'whatsapp_number' => '8801700000001',
            'email' => 'khangadget.bd@gmail.com',
            'website' => 'khangadget.com',
            'address' => 'Level 4, House 12, Road 5, Dhanmondi, Dhaka 1205, Bangladesh',
            'hours' => 'Sat – Thu · 10:00 AM – 9:00 PM',
            'social_facebook' => 'https://facebook.com/khansgadget',
            'social_youtube' => 'https://youtube.com/@khansgadget',
            'social_bikroy' => 'https://bikroy.com/en/shops/khangadgets',
            'social_bdstall' => 'https://bdstall.com/stall/2373',
            'social_daraz' => 'https://daraz.com.bd/shop/ki2kz4ne',
        ];

        foreach ($settings as $key => $value) {
            SiteSetting::create(['key' => $key, 'value' => $value]);
        }
    }
}
