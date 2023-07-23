<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'detail',
        'price',
        'stock',
        'discount',
        'Category_id'
    ];

    public function Category()
    {
        return $this->belongsTo(Category::class,'Category_id');
    }

    public function photos()
    {
        return $this->hasMany(Photo::class);
    }
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}
