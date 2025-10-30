<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    // Định nghĩa mối quan hệ một-nhiều với OrderDetail
    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class);  // Liên kết với model OrderDetail
    }
}

