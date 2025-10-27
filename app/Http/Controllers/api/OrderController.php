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

use function PHPSTORM_META\map;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // $allOrders = Order::withAggregate('user' , 'name')->get();
        $allOrders = Order::withAggregate('user' , 'name')->paginate(10);

        $response = [
            'message' => 'all orders retrieved successfully',
            'data' => $allOrders,
            'success' => true ,
            'status' => 200
        ];
        return response()->json($response , 200);

    }


    //=======================================


    public function myorders () {

        $myorders = Order::where('user_id' , Auth::user()->id )->get();
        $user_name = Auth::user()->name; // name for auth user

        if ($myorders->isEmpty() ) {
            return response() -> json([
                'message' => "orders not found for this user " . $user_name,
                'success' => false ,
                'status' => 404
            ] , 200);
        } else {

            $response = [
                'mesaage' => "orders for this user "  . $user_name . " retrived successfully",
                'success' => true,
                'data' => $myorders ,
                'status' => 200

            ];

            return response() -> json($response , 200 );
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

            return response()->json([
                'message' => "product {$productName} not available to orderd",
                'success' => false,
                'status' => 400
            ], 200);
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
    $response = [
        'message' => 'order created successfully',
        'success' => true,
        'Notification sent to user' => true,
        // 'data' => $order->load('products'),
        // 'data' => $data, // if you want less data you are manage it ///
        'order_id' => $order->id,
        'status' => 201
    ];

    return response()->json($response, 200);
}





    //=======================================================================



    public function controlStatus (string $id , Request $request) {

        $orderStatus = Order::find($id);
        if (!$orderStatus) {
          return response() -> json ([
              'message' => 'order not found to change status',
              'success' => false ,
              'status' => 404
          ] , 200);
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



        // user_name created order
        //product_name of order
        // category_name of product in order

     /*   $orderDetails = Order::with(['user:id,name' , 'products' => function($query) {
            $query->select('products.id' , 'products.name' , 'products.description' , 'products.price');
        }])->find($id);
*/
        $orderDetails = Order::with(['user:id,name' , 'products:name,price' , 'products.category:name'])->find($id);


        // $orderDetails = Order::withAggregate('products' , 'name')->find($id);
        // logger($orderDetails);

        if (!$orderDetails) {
            return response()->json([
                'message' => 'order not found',
                'success' => false ,
                'status' => 404
            ] , 200);
        }

        $response = [
            'message' => 'order details retrieved successfully',
            'data' => $orderDetails,
            'success' => true ,
            'status' => 200
        ];

        return response()->json($response , 200);
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
    public function destroy(Order $order)
    {

    }
}
