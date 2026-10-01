<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ProductSpecLabelCleanupTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_the_six_fixed_bold_labels_are_kept(): void
    {
        // RefreshDatabase wraps the test in a transaction that is rolled back,
        // so deferred FK checks never fire and no parent products are needed.
        DB::statement('PRAGMA defer_foreign_keys = ON');

        $rows = [
            [1, 'Processor', 'Core i7', 3],
            [1, 'RAM Type', 'DDR5', 2],
            [1, '𝐑𝐀𝐌:', '16 GB', 1],
            [1, 'Model', 'XPS 13', 0],
            [1, 'SSD', '512 GB', 4],
            [1, 'Storage', '1 TB', 5],
            [2, 'Warranty', '1 year', 0],
            [2, '𝐃𝐈𝐒𝐏𝐋𝐀𝐘:', '14"', 1],
        ];
        foreach ($rows as [$productId, $label, $value, $sort]) {
            DB::table('product_specs')->insert(['product_id' => $productId, 'label' => $label, 'value' => $value, 'sort_order' => $sort]);
        }

        (require database_path('migrations/2026_10_01_000000_keep_only_fixed_product_spec_labels.php'))->up();

        $this->assertSame(
            ['𝐌𝐎𝐃𝐄𝐋:' => 'XPS 13', '𝐏𝐑𝐎𝐂𝐄𝐒𝐒𝐎𝐑:' => 'Core i7', '𝐑𝐀𝐌:' => '16 GB', '𝐒𝐓𝐎𝐑𝐀𝐆𝐄:' => '512 GB'],
            DB::table('product_specs')->where('product_id', 1)->orderBy('sort_order')->pluck('value', 'label')->all()
        );
        $this->assertSame(
            ['𝐃𝐈𝐒𝐏𝐋𝐀𝐘:' => '14"'],
            DB::table('product_specs')->where('product_id', 2)->pluck('value', 'label')->all()
        );
        $this->assertEqualsCanonicalizing(
            ['RAM Type', 'Storage', 'Warranty'],
            DB::table('product_specs_removed_backup')->pluck('label')->all()
        );
    }
}
