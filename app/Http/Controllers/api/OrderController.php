<?php

// namespace App\Http\Controllers;

// use Illuminate\Http\Request;



namespace App\Http\Controllers\Api;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Requests\orderRequest;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Notifications\CreateOrder;
use App\Notifications\OrderstausUpdated;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use App\Traits\ApiResponseTrait;

use function PHPSTORM_META\map;

class OrderController extends Controller
{
    use ApiResponseTrait;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // $allOrders = Order::withAggregate('user' , 'name')->get();
        $allOrders = Order::withAggregate('user' , 'name')->paginate(10);
        return $this->successResponse($allOrders , 'all orders retrieved successfully' , 200);

    }


    //=======================================


    public function myorders () {

        $myorders = Order::where('user_id' , Auth::user()->id )->get();
        $user_name = Auth::user()->name; // name for auth user

        if ($myorders->isEmpty() ) {

            return $this->errorResponse( null , "orders not found for this user " . $user_name , 404);
        } else {


            return $this->successResponse( $myorders , "orders for this user "  . $user_name . " retrived successfully" , 200);
        }



    }




    //============================================================

    public function store(orderRequest $request)
{
    $validOrder = $request->validated();

    $totalPrice = 0;
    $points = 0;



    // ✅ Check if all products are valid and active before creating the order
    foreach ($validOrder['products'] as $productData) {
        $product = Product::find($productData['product_id']);

        if (!$product || $product->status == 'unactive') {
            $productName = $product ? $product->name : 'unknown product';

            return $this->errorResponse( null , "product {$productName} not available to orderd" , 400);
        }
    }

    // ✅ Create order only after validation
    $order = Order::create([
        'order_date' => now(),
        'points' => 0,
        'user_id' => Auth::user()->id,
        'totalPrice' => 0
    ]);

    // ✅ Attach products to order
    foreach ($validOrder['products'] as $productData) {

        $products = Product::find($productData['product_id']);

        $quantity = $productData['quantity']; // from request
        $price = $products->price; // from database
        $product_name = $products->name; // from database

        $order->products()->attach($productData['product_id'], [
            'product_name' => $product_name,
            'quantity' => $quantity,
            'price' => $price
        ]);

        $totalPrice += $price * $quantity;
    }

    // ✅ Calculate points after all products are processed
    if ($totalPrice >= 50) {
        $points = $totalPrice / 50;
    } else {
        $points = 1;
    }

    // ✅ Update order with total price and points
    $order->update([
        'totalPrice' => $totalPrice,
        'points' => $points
    ]);

    $user = Auth::user();

    $user->notify(new CreateOrder($totalPrice , $user->name , $order->id));
    // Notification::send($user , new CreateOrder($totalPrice , $user->name , $order->id));

    // ✅ Prepare data for response
    $data = $order->products->map(function ($product) {
        return [
            'product_name' => $product->name,
            'price' => $product->price,
            'quantity' => $product->pivot->quantity,
            'description' => $product->description
        ];
    });

    // ✅ Final response
    return $this->successResponse( $order->load('products') , 'order created successfully' , 201);
    // return $this->successResponse( $data , 'order created successfully' , 201); // if you want less data you are manage it ///
}





    //=======================================================================



    public function controlStatus (string $id , Request $request) {

        $orderStatus = Order::find($id);
        if (!$orderStatus) {

            return $this->errorResponse( null , 'order not found to change status' , 404);
        }
        // logger($orderStatus);
        $nameProductInOrder = $orderStatus->products->pluck('pivot.product_name')->join(' ,');
        // logger($product_name);

        $currentStatus = $orderStatus->status;
        $newStatus = $request->status;
        $allowedStatuses = ['pending' , 'processing','completed' , 'cancelled'];

        if (!in_array($newStatus , $allowedStatuses)) {
           return response() -> json([
               'message' => 'invalid status value',
               'allowed statuses' => $allowedStatuses,
               'status' => 400
           ] , 400);

       }



       // allowed transitions ===
           $validTransition = [
            'pending' => ['processing' , 'cancelled'],
            'processing' => ['completed' , 'cancelled'],
            'completed' => [],
            'cancelled' => []
        ];



        //====
        if (!in_array($newStatus , $validTransition[$currentStatus])) {
            return response() -> json ([
                'message' => "invalid status transition from {$currentStatus} to {$newStatus}" ,
                'allowed transions' => $validTransition,
                'status' => 400
            ],400 ); }






     $orderStatus->update([
        'status' => $request->status
       ]);



       $user_name = Auth::user()->name;
       $oderID = $orderStatus->id;
       $orderStatus->user->notify(new OrderstausUpdated($oderID , $currentStatus , $newStatus , $user_name));


       return response()->json([
        'message' => 'this status for order' ,
        'aboutOrder' => "the order for  {$nameProductInOrder} has status {$currentStatus}",
        'newStatus' => "the order updated it status successfully to {$newStatus}" ,
        'Notification sent to user' => true,
        'success' => true,
        'status' => 200
       ] , 200);
    }



    public function show(string $id)
    {



        $orderDetails = Order::with(['user:id,name' , 'products:name,price' , 'products.category:name'])->find($id);
        if (!$orderDetails) {

            return $this->errorResponse( null , 'order not found to show details' , 404);
        }


        return $this->successResponse( $orderDetails , 'order details retrieved successfully' , 200);
    }


    public function edit(Order $order)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Order $order)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $orderToDelete = Order::find($id);
        if (!$orderToDelete) {

            return $this->errorResponse( null , 'order not found to delete' , 404);
        }

        $orderToDelete->delete();

        return $this->successResponse( null , 'order deleted successfully' , 204);
    }
}
