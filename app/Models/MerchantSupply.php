<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MerchantSupply extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'supply_id',
        'price',
        'stock_quantity'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function supply()
    {
        return $this->belongsTo(Supply::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'merchant_supply_id');
    }

    /**
     * Vérifie si le stock est suffisant
     */
    public function hasSufficientStock($quantity)
    {
        return $this->stock_quantity >= $quantity;
    }

    /**
     * Décrémente le stock de manière sécurisée
     */
    public function safeDecrement($quantity)
    {
        if ($this->hasSufficientStock($quantity)) {
            return $this->decrement('stock_quantity', $quantity);
        }
        
        throw new \Exception("Stock insuffisant pour {$this->supply->name}");
    }
}