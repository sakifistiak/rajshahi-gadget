<?php
namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Seeder;

class BrandSeeder extends Seeder
{
    public function run(): void
    {
        $brands = [
            ['slug' => 'apple', 'name' => 'Apple'],
            ['slug' => 'dell', 'name' => 'Dell'],
            ['slug' => 'hp', 'name' => 'HP'],
            ['slug' => 'lenovo', 'name' => 'Lenovo'],
            ['slug' => 'asus', 'name' => 'Asus'],
            ['slug' => 'acer', 'name' => 'Acer'],
            ['slug' => 'khan-gadget', 'name' => 'Khan Gadget'],
        ];

        foreach ($brands as $brand) {
            Brand::create($brand);
        }
    }
}
