<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;


class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::orderBy('created_at', 'DESC')->paginate(10);
        return view('Admin.products.products', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::select('id', 'name')->orderBy('name')->get();
        $brands = Brand::select('id', 'name')->orderBy('name')->get();
        return view('admin.products.add_edit', compact('categories', 'brands'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd($request);
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:products,slug',
            'short_description' => 'required',
            'description' => 'required',
            'regular_price' => 'required',
            'SKU' => 'required',
            'stock_status' => 'required',
            'featured' => 'required',
            'quantity' => 'required',
            'image' => 'required|mimes:png,jpg,jpeg|max:2048',
            'category_id' => 'required',
            'brand_id' => 'required',
        ]);

        $product = new Product();
        $product->name = $request->name;
        $product->slug = Str::slug($request->name);
        $product->short_description = $request->short_description;
        $product->description = $request->description;
        $product->regular_price = $request->regular_price;
        $product->sale_price = $request->sale_price;
        $product->price=$request->sale_price ? $request->sale_price : $request->regular_price;
        $product->SKU = $request->SKU;
        $product->stock_status = $request->stock_status;
        $product->featured = $request->featured;
        $product->quantity = $request->quantity;
        $product->category_id = $request->category_id;
        $product->brand_id = $request->brand_id;

        $current_timestamp = Carbon::now()->timestamp;
        if ($request->hasfile('image')) {
            $image = $request->file('image');
            $file_extension = $image->extension();
            $image_name = $current_timestamp . '.' . $file_extension;
            $this->generateProductThumbnailImage($image, $image_name);
            $product->image = $image_name;
        }
        $gallery_arr = array();
        $gallery_images = "";
        $counter = 1;
        if ($request->hasfile('images')) {
            $allowedfileExtenstion = ['jpg', 'png', 'jpeg'];
            $files = $request->file('images');
            foreach ($files as $file) {
                $gextenstion = $file->getClientOriginalExtension();
                $gcheck = in_array($gextenstion, $allowedfileExtenstion);
                if ($gcheck) {
                    $gfilename = $current_timestamp . "-" . $counter . "." . $gextenstion;
                    $this->generateProductThumbnailImage($file, $gfilename);
                    array_push($gallery_arr, $gfilename);
                    $counter = $counter + 1;
                }
            }
            $gallery_images = implode(',', $gallery_arr);
        }
        $product->images = $gallery_images;
        $product->created_by = Auth::user()->id;
        $product->save();
        return redirect()->route('admin.products')->with('status', 'Product Has Been Added Successfully');

    }



    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        $categories = Category::select('id', 'name')->orderBy('name')->get();
        $brands = Brand::select('id', 'name')->orderBy('name')->get();
        return view('admin.products.add_edit', compact('categories', 'brands', 'product'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:products,slug,' . $product->id . ',id,deleted_at,NULL',
            'short_description' => 'required',
            'description' => 'required',
            'regular_price' => 'required',
            'SKU' => 'required',
            'stock_status' => 'required',
            'featured' => 'required',
            'quantity' => 'required',
            'image' => 'mimes:png,jpg,jpeg|max:2048',
            'category_id' => 'required',
            'brand_id' => 'required',
        ]);
        $product->name = $request->name;
        $product->slug = Str::slug($request->name);
        $product->short_description = $request->short_description;
        $product->description = $request->description;
        $product->regular_price = $request->regular_price;
        $product->sale_price = $request->sale_price;
        $product->price=$request->sale_price ? $request->sale_price : $request->regular_price;
        $product->SKU = $request->SKU;
        $product->stock_status = $request->stock_status;
        $product->featured = $request->featured;
        $product->quantity = $request->quantity;
        $product->category_id = $request->category_id;
        $product->brand_id = $request->brand_id;

        $current_timestamp = Carbon::now()->timestamp;
        if ($request->hasfile('image')) {
            if (File::exists(public_path('uploads/products') . '/' . $product->image)) {
                File::delete(public_path('uploads/products') . '/' . $product->image);
            }
            $image = $request->file('image');
            $file_extension = $image->extension();
            $image_name = $current_timestamp . '.' . $file_extension;
            $this->generateProductThumbnailImage($image, $image_name);
            $product->image = $image_name;
        }


        $gallery_arr = array();
        $gallery_images = "";
        $counter = 1;
        if ($request->hasfile('images')) {
            if($product->images){
                foreach(explode(',',$product->images) as $imagefile){
                    if (File::exists(public_path('uploads/products') . '/' . $imagefile)) {
                        File::delete(public_path('uploads/products') . '/' . $imagefile);
                    }
                }
            }
            $allowedfileExtenstion = ['jpg', 'png', 'jpeg'];
            $files = $request->file('images');
            foreach ($files as $file) {
                $gextenstion = $file->getClientOriginalExtension();
                $gcheck = in_array($gextenstion, $allowedfileExtenstion);
                if ($gcheck) {
                    $gfilename = $current_timestamp . "-" . $counter . "." . $gextenstion;
                    $this->generateProductThumbnailImage($file, $gfilename);
                    array_push($gallery_arr, $gfilename);
                    $counter = $counter + 1;
                }
            }
            $gallery_images = implode(',', $gallery_arr);
            $product->images = $gallery_images;

        }

        $product->updated_by = Auth::user()->id;
        $product->save();
        return redirect()->route('admin.products')->with('status', 'Product Has Been Updated Successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        if($product->image){
        if (File::exists(public_path('uploads/products') . '/' . $product->image)) {
            File::delete(public_path('uploads/products') . '/' . $product->image);
        }}
        if($product->images){
            foreach(explode(',',$product->images) as $imagefile){
                if (File::exists(public_path('uploads/products') . '/' . $imagefile)) {
                    File::delete(public_path('uploads/products') . '/' . $imagefile);
                }
            }
        }

        $product->deleted_by = Auth::user()->id;
        $product->save();
        $product->delete();

        return redirect()->route('admin.products')->with('status', 'Product Has Been Deleted Successfully');
    }

    public function generateProductThumbnailImage($image, $imageName)
    {
        $destinationPath = public_path('uploads/products');
        $img = Image::read($image->path());
        $img->cover(540, 689, "top");
        $img->resize(540, 689, function ($constraint) {
            $constraint->aspectRatio();
        })->save($destinationPath . '/' . $imageName);

    }
}
