<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlogPost extends Model
{
    protected $fillable = [
        'slug', 'title', 'excerpt', 'content', 'category_tag',
        'featured_image', 'read_minutes', 'author_name', 'author_role', 'published_at',
    ];

    protected function casts(): array
    {
        return [
            'content' => 'array',
            'published_at' => 'date',
        ];
    }
}
