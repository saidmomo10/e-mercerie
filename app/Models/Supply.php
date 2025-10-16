<?php

namespace App\Models;

use App\Models\Supply;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Supply extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'category', 'unit', 'description'];

    public function merchantSupplies()
    {
        return $this->hasMany(MerchantSupply::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}

