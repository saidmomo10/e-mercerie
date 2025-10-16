<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'couturier_id',
        'mercerie_id',
        'total_amount',
        'status',
    ];

    public function couturier()
    {
        return $this->belongsTo(User::class, 'couturier_id');
    }

    public function mercerie()
    {
        return $this->belongsTo(User::class, 'mercerie_id');
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
