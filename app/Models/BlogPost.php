<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class BlogPost extends Model
{
    protected $fillable = [
        'slug', 'title', 'excerpt', 'content', 'category_tag',
        'featured_image', 'read_minutes', 'author_name', 'author_role', 'published_at', 'is_featured',
    ];

    protected function casts(): array
    {
        return [
            'published_at' => 'date',
            'is_featured' => 'boolean',
        ];
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->whereNotNull('published_at')->where('published_at', '<=', now());
    }

    public function authorInitials(): string
    {
        $words = preg_split('/\s+/', trim($this->author_name));
        $initials = array_map(fn ($w) => mb_strtoupper(mb_substr($w, 0, 1)), array_slice($words, 0, 2));

        return implode('', $initials) ?: '?';
    }
}
