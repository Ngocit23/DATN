<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'order_id',          
        'product_variant_id',  
        'quantity',           
        'price',              
        'status',    
        'created_at',      
        'updated_at'
    ];
    // Định nghĩa mối quan hệ ngược lại với Order
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}

