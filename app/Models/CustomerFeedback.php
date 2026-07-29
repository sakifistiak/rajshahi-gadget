<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerFeedback extends Model
{
    protected $table = 'customer_feedbacks';

    protected $fillable = ['name', 'location', 'rating', 'message', 'date', 'image'];

    protected function casts(): array
    {
        return [
            'rating' => 'decimal:1',
            'date' => 'date',
        ];
    }
}
