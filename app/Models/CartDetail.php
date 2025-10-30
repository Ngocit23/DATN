<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class CartDetail extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'product_variant_id', 'quantity', 'subtotal', 'size_id'];

    public function productVariant()
    {
        return $this->belongsTo(ProductVariant::class);
    }
    
}
