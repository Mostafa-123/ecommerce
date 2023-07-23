<?php

namespace App\Http\Controllers;

use App\Http\responseTrait;
use App\Models\Order;
use App\Models\OrderItem;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class orderItemController extends Controller
{
    use responseTrait;

    public function addItem(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'quantity'=>'required|integer',
            'price' =>'required|numeric',
            'product_id'=>'required',
            'order_id'=>'required',
        ]);
        if ($validator->fails()) {
            return $this->response(null, $validator->errors(), 400);
        }
        try {
            DB::beginTransaction();
            $result = OrderItem::create([
                'quantity' => $request->quantity,
                'price' => $request->price,
                'product_id' => $request->product_id,
                'order_id' => $request->order_id,
            ]);
            DB::commit();
            if ($result) {
                return $this->response($result, 'done', 201);
            } else {
                return $this->response(null, 'item is not saved', 405);
            }
        } catch (Exception $e) {
            DB::rollback();
            return $this->response('', $e, 401);
        }
    }
    public function updateItem(Request $request, $item_id)
    {
        $item = OrderItem::find($item_id);

        if ($item) {
            try {
                DB::beginTransaction();
                $newData = [
                    'quantity' => $request->quantity ? $request->quantity : $item->quantity,
                    'price' => $request->price ? $request->price : $item->price,
                    'product_id' => $request->product_id ? $request->product_id : $item->product_id,
                    'order_id' => $request->order_id ? $request->order_id : $item->order_id,
                ];

                $item->update($newData);
                DB::commit();
                return $this->response($this->orderItemResources($item), 'item updated successfully', 200);
            } catch (Exception $e) {
                DB::rollback();
                return $this->response('', $e, 401);
            }
        } else {
            return $this->response('', 'product not  found', 404);
        }
    }


    public function destroyItem($id)
    {

        $result = OrderItem::find($id);

        if (!$result) {
            return $this->response(null, 'The item Not Found', 404);
        } else if ($result) {
            $result->delete();
            return $this->response('', 'The item deleted', 200);
        }
    }
    public function deleteAllItems(){
        $items=OrderItem::get();
        if($items){
            foreach($items as $item){
                $this->destroyItem($item->id);
            }
            return $this->response(''," items deleted succeffully",201);
        }return $this->response('',"not found items",404);


    }


    public function getItem($item_id) {
        $item=OrderItem::find($item_id);
        if($item){
            return $this->response($this->orderItemResources($item),"a item Data",201);
        }
        return $this->response('',"this item_id not found",401);
    }
    public function getAllItems(){
        $items=OrderItem::get();
        if($items){
            foreach($items as $item){
                $data[]=$this->orderItemResources($item);
            }
            return $this->response($data,"items returned successfuly",200);
        }return $this->response('',"somthing wrong",401);
    }
}
