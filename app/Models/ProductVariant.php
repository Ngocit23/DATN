<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    // Quan hệ ngược lại: 1 variant thuộc về 1 product
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function variants() {
        return $this->hasMany(ProductVariant::class);
    }
    
    public function brand() {
        return $this->belongsTo(Brand::class);
    }
    
    public function color()
    {
        return $this->belongsTo(Color::class, 'color_id');
    }
    public function size()
{
    return $this->belongsTo(Size::class);
}
    
}

