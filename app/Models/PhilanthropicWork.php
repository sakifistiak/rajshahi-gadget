<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PhilanthropicWork extends Model
{
    protected $fillable = ['title', 'slug', 'place', 'image', 'summary', 'content', 'date'];

    protected function casts(): array
    {
        return ['date' => 'date'];
    }
}
