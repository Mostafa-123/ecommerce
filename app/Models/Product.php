<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory,SoftDeletes;

    protected $id='id';

    public function category(){
        return $this->belongsTo(Category::class,'category_id');
        }

        public function brand(){
            return $this->belongsTo(Brand::class,'brand_id');
            }
            public function user(){
                return $this->belongsTo(User::class,'created_by');
            }
}
