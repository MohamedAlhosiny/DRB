<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    public $fillable = [
        'order_id' ,
        'points',
        'user_id'
    ];

    public function products() {
        return $this->belongsToMany(Product::class , 'order_product')
        ->withPivot('price' , 'quantity')
        ->withTimestamps();
    }

    public function user(){
        return $this->belongsTo(User::class , 'user_id');
    }
}
