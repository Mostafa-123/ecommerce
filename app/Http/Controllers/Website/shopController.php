<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class shopController extends Controller
{
    public function index(Request $request){
        //dd($request);

        $max_products_price= Product::max('price');;
        //Product::select(max('price'));
        $size=$request->query('size') ? $request->query('size') : 12;
        $order=$request->query('order') ? $request->query('order') : -1;
        $filterBrands=$request->query('brands');
        $filterCategories=$request->query('categories');
        $o_column="";
        $o_order="";
        $min_price=$request->query('min') ? $request->query('min') : 1;
        $max_price= $request->query('max') ? $request->query('max') : $max_products_price;
        switch($order){
            case 1:
                $o_column='created_at';
                $o_order='DESC';
            break;
            case 2:
                $o_column='created_at';
                $o_order='ASC';
            break;
            case 3:
                $o_column='price';
                $o_order='DESC';
            break;
            case 4:
                $o_column='price';
                $o_order='ASC';
            break;
            default:
            $o_column='id';
            $o_order='DESC';
        }
        $products=Product::where(function($query) use($filterBrands){
            return $filterBrands ?  $query->whereIn("brand_id", explode(',', $filterBrands)) : $query;
        })->
        where(function($query) use($filterCategories){
            return $filterCategories ?  $query->whereIn("category_id", explode(',', $filterCategories)) : $query;
        })
        ->where(function($query) use($min_price,$max_price){
            return $query->whereBetween('price',[$min_price,$max_price]);
        })
        ->orderBy($o_column,$o_order)->paginate($size);
        $brands=Brand::orderBy('name','ASC')->get();
        $categories=Category::orderBy('name','ASC')->get();
        return view('website.shop',compact('products','size','order',
        'brands','filterBrands','categories','filterCategories',
        'max_price','min_price','max_products_price'));
    }

    public function product_details($product_slug){
        $product=Product::where('slug',$product_slug)->first();
        $releated_products=Product::where('slug','<>',$product->slug)->get()->take(8);
        return view('website.product_details',compact('product','releated_products'));
    }
}
