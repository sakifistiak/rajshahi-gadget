<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Condition;
use App\Models\Product;
use Illuminate\Database\Seeder;

class KhanGadgetDummyProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            [
                'slug' => 'demo-asus-rog-strix-g16-rtx-5070ti',
                'name' => 'ASUS ROG Strix G16 Ryzen 9 RTX 5070Ti',
                'brand' => 'asus',
                'category' => 'gaming-laptops',
                'price' => 349999,
                'compare_at_price' => 365000,
                'cpu' => 'AMD Ryzen 9-8940HX, 16 Cores',
                'ram' => '16GB DDR5 5600MHz',
                'storage' => '1TB SSD',
                'display' => '16" 165Hz Display',
                'graphics' => 'NVIDIA RTX 5070Ti 12GB DDR7',
            ],
            [
                'slug' => 'demo-msi-vector-a16-rtx-5070ti',
                'name' => 'MSI Vector A16 HX Ryzen 9 RTX 5070Ti',
                'brand' => 'msi',
                'category' => 'gaming-laptops',
                'price' => 259999,
                'compare_at_price' => 265000,
                'cpu' => 'AMD Ryzen 9-8940HX, 16 Cores',
                'ram' => '16GB DDR5 5600MHz',
                'storage' => '1TB SSD',
                'display' => '16" QHD Display',
                'graphics' => 'NVIDIA RTX 5070Ti 12GB DDR7',
            ],
            [
                'slug' => 'demo-hp-omen-16-rtx-5050',
                'name' => 'HP OMEN Gaming 16 Ryzen 9 RTX 5050',
                'brand' => 'hp',
                'category' => 'gaming-laptops',
                'price' => 215000,
                'compare_at_price' => 220000,
                'cpu' => 'AMD Ryzen 9-8940HX, 16 Cores',
                'ram' => '16GB DDR5 5600MT/s',
                'storage' => '512GB SSD',
                'display' => '16" 144Hz Display',
                'graphics' => 'NVIDIA RTX 5050 8GB DDR7',
            ],
            [
                'slug' => 'demo-dell-alienware-16-aurora',
                'name' => 'Dell Alienware 16 Aurora Core 7 RTX 5050',
                'brand' => 'dell',
                'category' => 'gaming-laptops',
                'price' => 199999,
                'compare_at_price' => 210000,
                'cpu' => 'Intel Core 7 240H (15th Gen), 10 Cores',
                'ram' => '16GB DDR5 5600MHz',
                'storage' => '1TB SSD',
                'display' => '16" QHD 120Hz Display',
                'graphics' => 'NVIDIA RTX 5050 8GB DDR7',
            ],
            [
                'slug' => 'demo-lenovo-legion-5-rtx-5060',
                'name' => 'Lenovo Legion 5 Ryzen 7 RTX 5060',
                'brand' => 'lenovo',
                'category' => 'gaming-laptops',
                'price' => 199999,
                'compare_at_price' => 210000,
                'cpu' => 'AMD Ryzen 7 260',
                'ram' => '16GB DDR5 5600MHz',
                'storage' => '512GB SSD',
                'display' => '15.1" QHD 165Hz OLED Display',
                'graphics' => 'NVIDIA RTX 5060 8GB DDR7',
            ],
            [
                'slug' => 'demo-asus-tuf-gaming-a16-rtx-5060',
                'name' => 'ASUS TUF Gaming A16 Ryzen 7 RTX 5060',
                'brand' => 'asus',
                'category' => 'gaming-laptops',
                'price' => 198500,
                'compare_at_price' => 200000,
                'cpu' => 'AMD Ryzen 7 260',
                'ram' => '16GB DDR5 4800MHz',
                'storage' => '512GB SSD',
                'display' => '16" 165Hz Display',
                'graphics' => 'NVIDIA RTX 5060 8GB DDR7',
            ],
        ];

        $brandIds = Brand::pluck('id', 'slug');
        $categoryIds = Category::pluck('id', 'slug');
        $conditionId = Condition::where('slug', 'intact')->value('id');

        foreach ($products as $data) {
            $product = Product::firstOrCreate(
                ['slug' => $data['slug']],
                [
                    'name' => $data['name'],
                    'brand_id' => $brandIds[$data['brand']],
                    // Some local databases do not have the newer gaming-laptops slug yet.
                    'category_id' => $categoryIds[$data['category']] ?? $categoryIds['laptops'],
                    'condition_id' => $conditionId,
                    'price' => $data['price'],
                    'compare_at_price' => $data['compare_at_price'],
                    'rating' => 4.7,
                    'reviews_count' => 0,
                    'badge' => 'Demo',
                    'description' => $data['name'] . ' — demo product imported for catalog testing.',
                    'in_stock' => true,
                ]
            );

            if (! $product->wasRecentlyCreated) {
                continue;
            }

            $product->images()->create([
                'image_path' => '/assets/laptop-gaming-CRMi9E0Q.jpg',
                'is_primary' => true,
                'sort_order' => 0,
            ]);

            foreach ([$data['cpu'], $data['ram'], $data['storage'], $data['graphics']] as $sort => $highlight) {
                $product->highlights()->create(['text' => $highlight, 'sort_order' => $sort]);
            }

            foreach ([
                ['label' => 'Processor', 'value' => $data['cpu']],
                ['label' => 'RAM', 'value' => $data['ram']],
                ['label' => 'Storage', 'value' => $data['storage']],
                ['label' => 'Display', 'value' => $data['display']],
                ['label' => 'Graphics', 'value' => $data['graphics']],
            ] as $sort => $spec) {
                $product->specs()->create([
                    'label' => $spec['label'],
                    'value' => $spec['value'],
                    'sort_order' => $sort,
                ]);
            }
        }
    }
}
