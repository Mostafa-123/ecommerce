<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;

class CategoryController extends Controller
{
    /**
     * Display a listing of the categories.
     */
    public function index()
    {
        $categories = Category::orderBy('id', 'DESC')->paginate(10);
        return view('Admin.categories.categories', compact('categories'));
    }

    /**
     * Show the form for creating a new category.
     */
    public function create()
    {
        return view('admin.categories.add_edit');
    }

    /**
     * Store a newly created category in the database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:categories,slug,NULL,id,deleted_at,NULL',
            'image' => 'mimes:png,jpg,jpeg|max:2048'
        ]);

        $category = new Category();
        $category->name = $request->name;
        $category->slug = Str::slug($request->name);

        // Handle image upload and thumbnail generation
        if ($request->hasfile('image')) {
            $image = $request->file('image');
            $file_extension = $image->extension();
            $file_name = Carbon::now()->timestamp . '.' . $file_extension;
            $this->generateCategoryThumbnailImage($image, $file_name);

            $category->image = $file_name;
        }
        $category->created_by = Auth::user()->id;
        $category->save();

        return redirect()->route('admin.categories')->with('status', 'Category Has Been Added Successfully');
    }

    /**
     * Show the form for editing the specified category.
     */
    public function edit(Category $category)
    {
        return view('admin.categories.add_edit', compact('category'));
    }

    /**
     * Update the specified category in the database.
     */
    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:categories,slug,' . $category->id . ',id,deleted_at,NULL',
            'image' => 'mimes:png,jpg,jpeg|max:2048'
        ]);

        $category->name = $request->name;
        $category->slug = Str::slug($request->name);

        // Handle image upload and thumbnail generation
        if ($request->hasFile('image')) {
            // Delete the old image if it exists
            if (File::exists(public_path('uploads/categories') . '/' . $category->image)) {
                File::delete(public_path('uploads/categories') . '/' . $category->image);
            }

            // Save the new image and create the thumbnail
            $image = $request->file('image');
            $file_extension = $image->extension();
            $file_name = Carbon::now()->timestamp . '.' . $file_extension;
            $this->generateCategoryThumbnailImage($image, $file_name);
            $category->image = $file_name;
        }

        $category->updated_by = Auth::user()->id;
        $category->save();

        return redirect()->route('admin.categories')->with('status', 'Category Has Been Updated Successfully');
    }

    /**
     * Remove the specified category from the database.
     */
    public function destroy(Category $category)
    {
        // Delete the image file if it exists
        if (File::exists(public_path('uploads/categories') . '/' . $category->image)) {
            File::delete(public_path('uploads/categories') . '/' . $category->image);
        }

        $category->deleted_by = Auth::user()->id;
        $category->save();
        $category->delete();

        return redirect()->route('admin.categories')->with('status', 'Category Has Been Deleted Successfully');
    }

    /**
     * Generate the category thumbnail image.
     */
    public function generateCategoryThumbnailImage($image, $imageName)
    {
        $destinationPath = public_path('uploads/categories');
        $img = Image::read($image->path());
        $img->cover(124, 124, "top");
        $img->resize(124, 124, function ($constraint) {
            $constraint->aspectRatio();
        })->save($destinationPath . '/' . $imageName);
    }
}

