<?php

namespace App\Models;
use App\Models\Category;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'products';
    protected $fillable = [
        'name',
        'description',
        'price',
        'category_id',
        // 'status'
    ];

    const STATUS_UNACTIVE = false;
    const STATUS_ACTIVE = true;

    public function category() {
        return $this->belongsTo(Category::class , 'category_id');
    }

    public function orders() {
        return $this->belongsToMany(Order::class , 'order_product')
        ->withPivot('price' , 'quantity' , 'product_name')
        ->withTimestamps();
    }
}
