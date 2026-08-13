<?php

namespace App\Support;

use App\Models\FilterAttribute;
use App\Models\Product;

/**
 * Derives shop filter values (RAM, Storage, Connection, ...) straight from
 * the free-text Specifications an admin already enters per product — no
 * separate structured-data entry step. A FilterAttribute declares which
 * Specification label(s) to read (match_labels) and how to interpret the
 * value: 'range' pulls the largest number found (with GB/TB/MB unit
 * conversion to the attribute's unit), 'select' matches the value text
 * against the attribute's fixed option list.
 */
class ProductFilterSync
{
    private const UNIT_MULTIPLIERS = [
        'tb' => 1024 * 1024,
        'gb' => 1024,
        'mb' => 1,
        'kb' => 1 / 1024,
    ];

    public static function syncProduct(Product $product): void
    {
        $product->loadMissing('specs');
        $product->filterValues()->delete();

        $attributes = FilterAttribute::where('category_id', $product->category_id)->get();
        if ($attributes->isEmpty()) {
            return;
        }

        foreach ($attributes as $attribute) {
            $spec = static::findMatchingSpec($product, $attribute);
            if (! $spec || $spec->value === null || $spec->value === '') {
                continue;
            }

            if ($attribute->type === 'range') {
                $numeric = static::extractNumber($spec->value, $attribute->unit);
                if ($numeric !== null) {
                    $product->filterValues()->create([
                        'filter_attribute_id' => $attribute->id,
                        'numeric_value' => $numeric,
                    ]);
                }
            } else {
                $option = static::matchOption($spec->value, $attribute->optionList());
                if ($option !== null) {
                    $product->filterValues()->create([
                        'filter_attribute_id' => $attribute->id,
                        'text_value' => $option,
                    ]);
                }
            }
        }
    }

    /**
     * Re-derive filter values for every product in this attribute's category
     * — used when an admin adds/edits an attribute (e.g. changes which
     * Specification label it reads), so existing products pick it up
     * immediately instead of only on their next save.
     */
    public static function syncCategory(int $categoryId): void
    {
        Product::where('category_id', $categoryId)->with('specs')->get()
            ->each(fn (Product $product) => static::syncProduct($product));
    }

    private static function findMatchingSpec(Product $product, FilterAttribute $attribute)
    {
        $labels = array_map('mb_strtolower', $attribute->matchLabelList());

        return $product->specs->first(
            fn ($spec) => in_array(mb_strtolower(trim($spec->label)), $labels, true)
        );
    }

    private static function extractNumber(string $text, ?string $targetUnit): ?float
    {
        // Matches "16", "16GB", "1.5 TB" etc; picks the largest number found
        // so ranges like "8/16GB" resolve to the fuller spec (16).
        if (! preg_match_all('/(\d+(?:\.\d+)?)\s*(TB|GB|MB|KB)?/i', $text, $matches, PREG_SET_ORDER)) {
            return null;
        }

        $best = null;
        foreach ($matches as $match) {
            $value = (float) $match[1];
            $unit = strtolower($match[2] ?? '');

            if ($unit && $targetUnit) {
                $value = static::convert($value, $unit, strtolower($targetUnit));
            }

            if ($best === null || $value > $best) {
                $best = $value;
            }
        }

        return $best;
    }

    private static function convert(float $value, string $fromUnit, string $toUnit): float
    {
        $fromFactor = self::UNIT_MULTIPLIERS[$fromUnit] ?? 1;
        $toFactor = self::UNIT_MULTIPLIERS[$toUnit] ?? 1;

        return $value * $fromFactor / $toFactor;
    }

    private static function matchOption(string $text, array $options): ?string
    {
        foreach ($options as $option) {
            if (mb_stripos($text, $option) !== false) {
                return $option;
            }
        }

        return null;
    }
}
