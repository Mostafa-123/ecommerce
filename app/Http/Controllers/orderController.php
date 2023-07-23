<?php

namespace App\Http\Controllers;

use App\Http\responseTrait;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class orderController extends Controller
{
    use responseTrait;

    public function addOrder(Request $request)
    {
        $user=Auth::guard('users')->user();
        $productData = $request->input('products', []);
        if (empty($productData)) {
            return response()->json(['message' => 'No products provided for the order'], 400);
        }
        try {
            DB::beginTransaction();
            $order = new Order();
            $order->user_id = $user->id;
            $order->status = 'pending'; // You can set the initial status as 'pending'
            $order->total_amount = 0; // We'll calculate the total amount as we add the items
            $order->save();

            $totalAmount = 0;

            foreach ($productData as $item) {
                $product = Product::find($item['product_id']);
                if (!$product || $item['quantity'] <= 0 || $item['quantity'] > $product->stock) {
                    throw new Exception('Invalid product or quantity');
                }

                $orderItem = new OrderItem();
                $orderItem->order_id = $order->id;
                $orderItem->product_id = $product->id;
                $orderItem->quantity = $item['quantity'];
                $orderItem->price = $product->price;
                $orderItem->save();

                $totalAmount += $product->price * $item['quantity'];

                // Update product stock
                $product->stock -= $item['quantity'];
                $product->save();
            }

            // Update the order's total amount
            $order->total_amount = $totalAmount;
            $order->save();
            DB::commit();
            if ($order) {
                return $this->response($this->orderResources($order), 'done', 201);
            } else {
                return $this->response(null, 'order is not saved', 405);
            }
        } catch (Exception $e) {
            DB::rollback();
            return $this->response('', $e, 401);
        }
    }
    public function updateOrderStatus(Request $request, $order_id)
    {
        $order = Order::find($order_id);

        if ($order) {
            try {
                DB::beginTransaction();
                $newData = [
                    'status' => $request->status ? $request->status : $order->status,
                ];

                $order->update($newData);
                DB::commit();
                return $this->response($this->orderResources($order), 'order updated successfully', 200);
            } catch (Exception $e) {
                DB::rollback();
                return $this->response('', $e, 401);
            }
        } else {
            return $this->response('', 'product not  found', 404);
        }
    }


    public function destroyOrder($id)
    {

        $result = Order::find($id);

        $items=$result->orderItems;
        foreach($items as $item){
            $product = Product::find($item['product_id']);
            $product->stock+=$item['quantity'];
            $item->delete();
        }
        if (!$result) {
            return $this->response(null, 'The order Not Found', 404);
        } else if ($result) {
            $result->delete();
            return $this->response('', 'The order deleted', 200);
        }
    }
    public function deleteAllCategories(){
        $orders=Order::get();
        if($orders){
            foreach($orders as $order){
                $this->destroyOrder($order->id);
            }
            return $this->response(''," orders deleted succeffully",201);
        }return $this->response('',"not found orders",404);


    }


    public function getOrder($order_id) {
        $order=Order::find($order_id);
        if($order){
            return $this->response($this->orderResources($order),"a order Data",201);
        }
        return $this->response('',"this order_id not found",401);
    }

    public function getAllOrders(){
        $orders=Order::get();
        if($orders){
            foreach($orders as $order){
                $data[]=$this->orderResources($order);
            }
            return $this->response($data,"orders returned successfuly",200);
        }return $this->response('',"somthing wrong",401);
    }

    public function getAllItemsInOrder($order_id){
        $order=Order::find($order_id);
        if($order){
            $items=$order->orderItems;
            if($items){
                foreach($items as $item){
                    $data[]=$this->orderItemResources($item);
                }
            }
            return $this->response($data,"items returned successfuly",200);
        }return $this->response('',"somthing wrong",401);
    }
}
