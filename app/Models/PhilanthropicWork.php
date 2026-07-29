<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PhilanthropicWork extends Model
{
    protected $fillable = ['title', 'place', 'image', 'summary', 'date'];

    protected function casts(): array
    {
        return ['date' => 'date'];
    }
}
