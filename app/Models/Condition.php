<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Condition extends Model
{
    protected $fillable = ['slug', 'label', 'short', 'tagline'];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
