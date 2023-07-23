<?php

namespace App\Http\Controllers;

use App\Http\responseTrait;
use App\Models\Photo;
use App\Models\Product;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class productController extends Controller
{

    use responseTrait;

    public function addProduct(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'=>'required|max:255',
            'detail'=>'required',
            'price'=>'required',
            'stock'=>'required',
            'discount'=>'required',
            'Category_id'=>'required',
        ]);
        if ($validator->fails()) {
            return $this->response(null, $validator->errors(), 400);
        }
        try {
            DB::beginTransaction();
            $result = Product::create([
                'name' => $request->name,
                'detail' => $request->detail,
                'price' => $request->price,
                'stock' => $request->stock,
                'discount' => $request->discount,
                'Category_id' => $request->Category_id,
            ]);
            if (($request->photos)) {
                if($request->photos[0]){
                    for ($i = 0; $i < count($request->photos); $i++) {
                        $path = $this->uploadMultiFile($request, $i, 'productPhotos', 'photos');
                        Photo::create([
                            'photoname' => $path,
                            'product_id' => $result->id,
                        ]);
                    }
                }}
            DB::commit();
            if ($result) {
                return $this->response($this->productResources($result), 'done', 201);
            } else {
                return $this->response(null, 'product is not saved', 405);
            }
        } catch (Exception $e) {
            DB::rollback();
            return $this->response('', $e, 401);
        }
    }
    //up
    public function updateProduct(Request $request, $product_id)
    {
        $product = Product::find($product_id);

        if ($product) {
            try {
                DB::beginTransaction();
                $photos = $product->photos;
                if (($request->photos)) {
                    if ($request->photos[0]) {
                        if ($photos) {
                            for ($i = 0; $i < count($photos); $i++) {
                                $path = $photos[$i]->photoname;

                                $photo = Photo::where('photoname', $path)->get();
                                // print($photo[0]);die;
                                $photo[0]->delete();
                                $this->deleteFile($path);
                            }
                            for ($i = 0; $i < count($request->photos); $i++) {
                                $path = $this->uploadMultiFile($request, $i, 'productPhotos', 'photos');
                                Photo::create([
                                    'photoname' => $path,
                                    'product_id' => $product->id,
                                ]);
                            }
                        } else if ($photos == null) {
                            for ($i = 0; $i < count($request->photos); $i++) {
                                $path = $this->uploadMultiFile($request, $i, 'productPhotos', 'photos');
                                Photo::create([
                                    'photoname' => $path,
                                    'product_id' => $product->id,
                                ]);
                            }
                        }
                    }
                }

                $newData = [
                    'name' => $request->name ? $request->name : $product->name,
                    'detail' => $request->detail ? $request->detail : $product->detail,
                    'price' => $request->price ? $request->price : $product->price,
                    'stock' => $request->stock ? $request->stock : $product->stock,
                    'discount' => $request->discount ? $request->discount : $product->discount,
                ];

                $product->update($newData);
                DB::commit();
                return $this->response($this->productResources($product), 'product updated successfully', 200);
            } catch (Exception $e) {
                DB::rollback();
                return $this->response('', $e, 401);
            }
        } else {
            return $this->response('', 'product not  found', 404);
        }
    }

    public function destroyProduct($id)
    {

        $result = Product::find($id);

        if (!$result) {
            return $this->response(null, 'The Product request Not Found', 404);
        } else if ($result) {
            $photos = $result->photos;
            if ($photos) {
                for ($i = 0; $i < count($photos); $i++) {
                    $path = $photos[$i]->photoname;
                    $this->deleteFile($path);
                }
            }
            $result->delete();
            return $this->response('', 'The product request deleted', 200);
        }
    }
    public function deleteAllProducts(){
            $products=Product::get();
            if($products){
                foreach($products as $product){
                    $this->destroyProduct($product->id);
                }
                return $this->response(''," products deleted succeffully",201);
            }return $this->response('',"not found products",404);


    }

    public function getProduct($product_id) {
        $product=Product::find($product_id);
        if($product){
            return $this->response($this->productResources($product),"a product Data",201);
        }
        return $this->response('',"this product_id not found",401);
    }
    public function getAllProducts(){
        $products=Product::get();
        if($products){
            foreach($products as $product){
                $data[]=$this->productResources($product);
            }
            return $this->response($data,"products returned successfuly",200);
        }return $this->response('',"somthing wrong",401);
    }


    public function getProductPhoto($product_id, $photo_id)
    {
        $product = Product::find($product_id);
        if ($product) {
            $photo = Photo::find($photo_id);
            if ($photo) {
                return $this->getFile($photo->photoname);
            }
            return $this->response('', "This product doesn't has photo", 401);
        }
        return $this->response('', 'this product_id not found', 401);
    }
}
