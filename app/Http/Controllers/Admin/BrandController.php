<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;

class BrandController extends Controller
{
    /**
     * Display a listing of the brands.
     */
    public function index()
    {
        $brands = Brand::orderBy('id', 'DESC')->paginate(10);
        return view('Admin.brands.brands', compact('brands'));
    }

    /**
     * Show the form for creating a new brand.
     */
    public function create()
    {
        return view('admin.brands.add_edit');
    }

    /**
     * Store a newly created brand in the database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:brands,slug,NULL,id,deleted_at,NULL',
            'image' => 'mimes:png,jpg,jpeg|max:2048'
        ]);

        $brand = new Brand();
        $brand->name = $request->name;
        $brand->slug = Str::slug($request->name);

        // Handle the image upload and thumbnail creation
        if ($request->hasFile('image')) {
        $image = $request->file('image');
        $file_extension = $image->extension();
        $file_name = Carbon::now()->timestamp . '.' . $file_extension;
        $this->generateBrandThumbnailImage($image, $file_name);

        $brand->image = $file_name;}
        $brand->created_by = Auth::user()->id;
        $brand->save();

        return redirect()->route('admin.brands')->with('status', 'Brand Has Been Added Successfully');
    }

    /**
     * Show the form for editing the specified brand.
     */
    public function edit(Brand $brand)
    {
        return view('admin.brands.add_edit', compact('brand'));
    }

    /**
     * Update the specified brand in the database.
     */
    public function update(Request $request, Brand $brand)
    {
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:brands,slug,' . $brand->id . ',id,deleted_at,NULL',
            'image' => 'mimes:png,jpg,jpeg|max:2048'
        ]);

        $brand->name = $request->name;
        $brand->slug = Str::slug($request->name);

        // Handle the image upload and thumbnail creation
        if ($request->hasFile('image')) {
            // Delete old image
            if (File::exists(public_path('uploads/brands') . '/' . $brand->image)) {
                File::delete(public_path('uploads/brands') . '/' . $brand->image);
            }

            // Save new image
            $image = $request->file('image');
            $file_extension = $image->extension();
            $file_name = Carbon::now()->timestamp . '.' . $file_extension;
            $this->generateBrandThumbnailImage($image, $file_name);
            $brand->image = $file_name;
        }

        $brand->updated_by = Auth::user()->id;
        $brand->save();

        return redirect()->route('admin.brands')->with('status', 'Brand Has Been Updated Successfully');
    }

    /**
     * Remove the specified brand from the database.
     */
    public function destroy(Brand $brand)
    {
        // Delete the brand image
        if (File::exists(public_path('uploads/brands') . '/' . $brand->image)) {
            File::delete(public_path('uploads/brands') . '/' . $brand->image);
        }

        $brand->deleted_by = Auth::user()->id;
        $brand->save();
        $brand->delete();

        return redirect()->route('admin.brands')->with('status', 'Brand Has Been Deleted Successfully');
    }

    /**
     * Generate a thumbnail image for the brand.
     */
    public function generateBrandThumbnailImage($image, $imageName)
    {
        $destinationPath = public_path('uploads/brands');
        $img = Image::read($image->path());
        $img->cover(124, 124, "top");
        $img->resize(124, 124, function ($constraint) {
            $constraint->aspectRatio();
        })->save($destinationPath . '/' . $imageName);
    }
}
