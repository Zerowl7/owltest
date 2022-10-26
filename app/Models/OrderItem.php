<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;
    
    public function product()
    {
        return  $this->belongsTo(Product::class);
    }

    public function order()
    {
           return   $this->belongsTo(Order::class);

    }

    public function scopeWithOrder($query, $orderId)
    {
      //  $query->whereHas('order_id', function ($q) use ($orderId) {
            $query->where('order_id', $orderId);
      //  });
    }
}
