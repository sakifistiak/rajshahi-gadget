<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PhilanthropicWork extends Model
{
    protected $fillable = ['title', 'slug', 'image', 'video_url', 'content'];

    public function getYoutubeEmbedUrlAttribute(): ?string
    {
        if (! $this->video_url) {
            return null;
        }

        if (! preg_match('/(?:youtu\.be\/|youtube\.com\/(?:watch\?v=|embed\/|shorts\/))([A-Za-z0-9_-]{11})/', trim($this->video_url), $matches)) {
            return null;
        }

        return "https://www.youtube.com/embed/{$matches[1]}";
    }
}
