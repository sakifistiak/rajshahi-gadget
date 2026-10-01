<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Every product keeps only the six owner-chosen spec rows, renamed to the exact
 * Unicode-bold labels the admin form uses (see CLAUDE.md, "Product
 * specifications vs filters"). Plain-text variants such as "Processor" or
 * "SSD" are renamed and keep their value; any other row (RAM Type, Warranty,
 * ...) is removed. Removed rows are copied to product_specs_removed_backup
 * first, so they can be restored by hand if needed.
 */
return new class extends Migration
{
    private const LABELS = [
        'model' => '𝐌𝐎𝐃𝐄𝐋:',
        'processor' => '𝐏𝐑𝐎𝐂𝐄𝐒𝐒𝐎𝐑:',
        'speed' => '𝐒𝐏𝐄𝐄𝐃:',
        'ram' => '𝐑𝐀𝐌:',
        'storage' => '𝐒𝐓𝐎𝐑𝐀𝐆𝐄:',
        'display' => '𝐃𝐈𝐒𝐏𝐋𝐀𝐘:',
    ];

    private const ALIASES = [
        'cpu' => 'processor',
        'processor speed' => 'speed',
        'memory' => 'ram',
        'ssd' => 'storage',
        'storage capacity' => 'storage',
        'screen' => 'display',
    ];

    public function up(): void
    {
        if (! Schema::hasTable('product_specs_removed_backup')) {
            Schema::create('product_specs_removed_backup', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('spec_id');
                $table->unsignedBigInteger('product_id');
                $table->string('label');
                $table->text('value');
                $table->integer('sort_order')->default(0);
                $table->timestamp('removed_at')->nullable();
            });
        }

        $order = array_flip(array_keys(self::LABELS));

        DB::transaction(function () use ($order) {
            $specs = DB::table('product_specs')->orderBy('product_id')->orderBy('sort_order')->orderBy('id')->get();
            $seen = [];
            $remove = [];

            foreach ($specs as $spec) {
                $key = $this->canonicalKey($spec->label);

                if ($key === null || isset($seen[$spec->product_id][$key])) {
                    $remove[] = $spec;

                    continue;
                }

                $seen[$spec->product_id][$key] = true;
                DB::table('product_specs')->where('id', $spec->id)->update([
                    'label' => self::LABELS[$key],
                    'sort_order' => $order[$key],
                ]);
            }

            foreach (array_chunk($remove, 500) as $chunk) {
                DB::table('product_specs_removed_backup')->insert(array_map(fn ($spec) => [
                    'spec_id' => $spec->id,
                    'product_id' => $spec->product_id,
                    'label' => $spec->label,
                    'value' => $spec->value,
                    'sort_order' => $spec->sort_order,
                    'removed_at' => now(),
                ], $chunk));
                DB::table('product_specs')->whereIn('id', array_column($chunk, 'id'))->delete();
            }
        });
    }

    public function down(): void
    {
        // Data cleanup; the removed rows stay in product_specs_removed_backup.
    }

    private function canonicalKey(string $label): ?string
    {
        // NFKC turns the Unicode-bold letters back into plain A-Z.
        $plain = class_exists(Normalizer::class) ? Normalizer::normalize($label, Normalizer::FORM_KC) : $label;
        $plain = mb_strtolower(trim(preg_replace('/\s+/u', ' ', rtrim(trim((string) $plain), ':'))));
        $plain = self::ALIASES[$plain] ?? $plain;

        return array_key_exists($plain, self::LABELS) ? $plain : null;
    }
};
