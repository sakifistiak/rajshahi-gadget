<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class CustomPage extends Model
{
    use HasFactory;

    /**
     * Pixel height for the embedded Google Form on wide screens (the page adds room on phones,
     * where the questions wrap onto more lines).
     */
    public const DEFAULT_GOOGLE_FORM_HEIGHT = 1200;

    protected $fillable = [
        'title',
        'slug',
        'meta_title',
        'meta_description',
        'content',
        'google_form_url',
        'google_form_height',
        'is_active',
        'show_title',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'show_title' => 'boolean',
        'google_form_height' => 'integer',
    ];

    public function locations(): HasMany
    {
        return $this->hasMany(CustomPageLocation::class)->orderBy('sort_order');
    }

    /**
     * Auto generate slug on create/update if empty.
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($page) {
            if (empty($page->slug)) {
                $page->slug = Str::slug($page->title);
            } else {
                $page->slug = Str::slug($page->slug);
            }
        });
    }
}
