<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerSpotlight extends Model
{
    protected $fillable = ['name', 'location', 'product', 'image', 'quote', 'date'];

    protected function casts(): array
    {
        return ['date' => 'date'];
    }
}
