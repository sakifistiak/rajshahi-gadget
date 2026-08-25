<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomPageLocation extends Model
{
    protected $fillable = [
        'custom_page_id',
        'image_path',
        'name',
        'address',
        'phone',
        'map_link',
        'details',
        'sort_order',
    ];

    public function page(): BelongsTo
    {
        return $this->belongsTo(CustomPage::class, 'custom_page_id');
    }
}
