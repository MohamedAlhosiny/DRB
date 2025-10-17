<?php

// namespace App\Http\Controllers;

// use Illuminate\Http\Request;



namespace App\Http\Controllers\Api;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Requests\orderRequest;
use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;



class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $allOrders = Order::all();

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(orderRequest $request)
    {
        $validOrder = $request->validated();
        $order = Order::create([
            'order_date' => now(),
            'points' => 0,
            'user_id' => Auth::user()->id,
            'totalPrice' => 0
        ]);

        $totalPrice = 0 ;
        $points = 0 ;

        //products array from orderRequest !!!!!!!!!!!!!!!!!
        foreach($validOrder['products'] as $productData){

            $products = Product::find($productData['product_id']);
            // $products = Product::where('name' ,$productData['product_name']);

            if ( !$products || $products->status == 'unactive') {

            return response()->json([
                'message' => "product {$products->name} not available to orderd" ,
                'success' => false ,
                'status' => 400
            ] , 200);
            }
            $quantity = $productData['quantity']; // from request
            $price = $products->price; // from database
            $product_name = $products->name;
            $order->products()->attach($productData['product_id'], [
                'product_name' => $product_name,
                'quantity' => $quantity,
                'price' => $price
            ]);

            $totalPrice += $price * $quantity;
            if ($totalPrice >= 50) {
                $points = $totalPrice / 50;
            } else {

                $points = 1 ;
            }
        }



        $order->update([
            'totalPrice' => $totalPrice,
            'points' => $points
        ]);


        $data = $order->products->map(function ($product) {
            return  [
                'product_name' => $product->name,
                'price' => $product->price,
                'quantity' => $product->pivot->quantity,
                'description' => $product->description
            ];
        });

        $response = [
            'message' => 'order created successfully' ,
            'success' => true ,
            'data' => $order->load('products'),
            // 'data' => $data, // لو محتاج داتا اقل انت متحكم فيها
            'status' => 201
        ];

        return response() -> json($response , 200);


    }

    public function show(Order $order)
    {

    }

    /**
     * Show the form for editing the specified resource.
     */
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
        //
    }
}
