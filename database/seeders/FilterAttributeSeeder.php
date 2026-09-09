<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\FilterAttribute;
use Illuminate\Database\Seeder;

class FilterAttributeSeeder extends Seeder
{
    public function run(): void
    {
        $definitions = [
            'laptops' => $this->laptopFilters(),
            'gaming-laptops' => $this->laptopFilters(),
        ];

        foreach ($definitions as $categorySlug => $attributes) {
            $category = Category::where('slug', $categorySlug)->first();

            if (! $category) {
                continue;
            }

            foreach ($attributes as $attribute) {
                FilterAttribute::updateOrCreate(
                    [
                        'category_id' => $category->id,
                        'key' => $attribute['key'],
                    ],
                    array_merge($attribute, ['category_id' => $category->id])
                );
            }

        }
    }

    private function laptopFilters(): array
    {
        return [
            [
                'key' => 'processor',
                'label' => 'Processor',
                'match_labels' => 'Processor, CPU',
                'type' => 'select',
                'unit' => null,
                'options' => 'Core i3, Core i5, Core i7, Core i9, Ryzen 3, Ryzen 5, Ryzen 7, Ryzen 9, Apple M1, Apple M2, Apple M3, Apple M4',
                'sort_order' => 10,
            ],
            [
                'key' => 'ram',
                'label' => 'RAM',
                'match_labels' => 'RAM, Memory',
                'type' => 'select',
                'unit' => null,
                'options' => '4 GB, 8 GB, 12 GB, 16 GB, 24 GB, 32 GB, 64 GB',
                'sort_order' => 20,
            ],
            [
                'key' => 'ram_type',
                'label' => 'RAM Type',
                'match_labels' => 'RAM Type, Memory Type',
                'type' => 'select',
                'unit' => null,
                'options' => 'DDR3, DDR4, DDR5, LPDDR4, LPDDR4X, LPDDR5, LPDDR5X',
                'sort_order' => 30,
            ],
            [
                'key' => 'storage_type',
                'label' => 'Storage Type',
                'match_labels' => 'Storage, Storage Type',
                'type' => 'select',
                'unit' => null,
                'options' => 'NVMe, SSD, HDD, eMMC',
                'sort_order' => 40,
            ],
            [
                'key' => 'storage_capacity',
                'label' => 'Storage Capacity',
                'match_labels' => 'Storage, Storage Capacity',
                'type' => 'range',
                'unit' => 'GB',
                'options' => null,
                'sort_order' => 50,
            ],
            [
                'key' => 'graphics_type',
                'label' => 'Graphics Type',
                'match_labels' => 'Graphics, GPU, Graphics Type',
                'type' => 'select',
                'unit' => null,
                'options' => 'RTX, GTX, Radeon, Iris, Integrated, Apple',
                'sort_order' => 60,
            ],
            [
                'key' => 'operating_system',
                'label' => 'Operating System',
                'match_labels' => 'Operating System, OS',
                'type' => 'select',
                'unit' => null,
                'options' => 'Windows, macOS, Linux, DOS, ChromeOS',
                'sort_order' => 70,
            ],
        ];
    }
}
