<?php

namespace App\Http\Controllers;

use App\Http\responseTrait;
use App\Models\Category;
use App\Models\Photo;
use App\Models\Product;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    use responseTrait;

    public function addCategory(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'=>'required|max:255',
            'description'=>'required',
        ]);
        if ($validator->fails()) {
            return $this->response(null, $validator->errors(), 400);
        }
        try {
            DB::beginTransaction();
            $result = Category::create([
                'name' => $request->name,
                'description' => $request->description,
            ]);
            DB::commit();
            if ($result) {
                return $this->response($result, 'done', 201);
            } else {
                return $this->response(null, 'category is not saved', 405);
            }
        } catch (Exception $e) {
            DB::rollback();
            return $this->response('', $e, 401);
        }
    }
    public function updateCategory(Request $request, $category_id)
    {
        $category = Category::find($category_id);

        if ($category) {
            try {
                DB::beginTransaction();
                $newData = [
                    'name' => $request->name ? $request->name : $category->name,
                    'description' => $request->description ? $request->description : $category->description,
                ];

                $category->update($newData);
                DB::commit();
                return $this->response($category, 'category updated successfully', 200);
            } catch (Exception $e) {
                DB::rollback();
                return $this->response('', $e, 401);
            }
        } else {
            return $this->response('', 'product not  found', 404);
        }
    }

    public function destroyCategory($id)
    {

        $result = Category::find($id);

        if (!$result) {
            return $this->response(null, 'The category Not Found', 404);
        } else if ($result) {
            $result->delete();
            return $this->response('', 'The category deleted', 200);
        }
    }
    public function deleteAllCategories(){
        $Categories=Category::get();
        if($Categories){
            foreach($Categories as $Category){
                $this->destroyCategory($Category->id);
            }
            return $this->response(''," Categories deleted succeffully",201);
        }return $this->response('',"not found products",404);


    }

    public function getCategory($category_id) {
        $Category=Category::find($category_id);
        if($Category){
            return $this->response($Category,"a Category Data",201);
        }
        return $this->response('',"this Category_id not found",401);
    }
    public function getAllCategories(){
        $Categories=Category::get();
        if($Categories){
            foreach($Categories as $Category){
                $data[]=$Category;
            }
            return $this->response($data,"Categories returned successfuly",200);
        }return $this->response('',"somthing wrong",401);
    }
    public function getAllProductsInCategory($category_id){
        $category=Category::find($category_id);
        if($category){
            $products=$category->products;
            if($products){
                foreach($products as $product){
                    $data[]=$this->productResources($product);
                }
        }
            return $this->response($data,"products returned successfuly",200);
        }return $this->response('',"somthing wrong",401);
    }


}
