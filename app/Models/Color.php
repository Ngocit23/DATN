<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Color extends Model
{
    public function productVariants()
{
    return $this->hasMany(ProductVariant::class, 'color_id');
}

}
