<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MerchantSupply extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'supply_id', 'price', 'stock_quantity'];

    public function mercerie()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function supply()
    {
        return $this->belongsTo(Supply::class);
    }
}

