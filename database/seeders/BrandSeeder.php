<?php
namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Seeder;

class BrandSeeder extends Seeder
{
    public function run(): void
    {
        $brands = [
            ['slug' => 'apple', 'name' => 'Apple', 'logo_path' => '/media/brands/apple.svg'],
            ['slug' => 'dell', 'name' => 'Dell', 'logo_path' => '/media/brands/dell.svg'],
            ['slug' => 'hp', 'name' => 'HP', 'logo_path' => '/media/brands/hp.svg'],
            ['slug' => 'lenovo', 'name' => 'Lenovo', 'logo_path' => '/media/brands/lenovo.svg'],
            ['slug' => 'asus', 'name' => 'Asus', 'logo_path' => '/media/brands/asus.svg'],
            ['slug' => 'acer', 'name' => 'Acer', 'logo_path' => '/media/brands/acer.svg'],
            ['slug' => 'khan-gadget', 'name' => 'Khan Gadget', 'logo_path' => '/media/b3ca13-kg-lockup-v2.png'],
        ];

        foreach ($brands as $brand) {
            Brand::updateOrCreate(['slug' => $brand['slug']], $brand);
        }
    }
}
