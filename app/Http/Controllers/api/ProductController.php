<?php

namespace App\Http\Controllers\api;

use App\Models\Product;

use App\Http\Controllers\Controller;
use GuzzleHttp\Handler\Proxy;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $product = Product::paginate(10);

        $response = [
            'message' => 'data retrieved successfully',
            'success' => true,
            'data' => $product,
            'status' => 200
        ];

        return response()->json($response, 200);
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
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'string|required|min:3',
            'price' => 'required',
            'desc' => 'min:8'
        ]);

        $product =  Product::create([
            'name' => $request->name,
            'desc' => $request->desc,
            'price' => $request->price,
        ]);

        if ($product) {
            $response = [
                'message' => 'data stored successfully',
                'success' => true,
                'data' => $product->name,
                'status' => 200
            ];

            return response()->json($response, 200);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $product = Product::find($id);
        if (!$product) {
            $response = [
                'message' => 'this product not found',
                'success' => false,
                'status' => 404
            ];

            return response()->json($response, 200);
        }

        $response = [
            'message' => "product retrieved successfully",
            'product' => $product,
            'success' => true,
            'status' => 200,

        ];

        return response()->json($response, 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {

    // dd($request->all());

        $product = Product::find($id);


        if (!$product) {
            $response = [
                'message' => 'this product not found to update',
                'success' => false,
                'status' => 404
            ];
            return response()->json($response, 200);
        }


            $request->validate([
                'name' => 'string|min:3|nullable',
                'desc' => 'min:8|nullable',
                'price' => 'nullable'
            ]);

            // dd($request->all());
            $name = request()->name;
            $desc = request()->desc;
            $price = request()->price;

            $product->update([
                'name' => $name ?? $product->name,
                'desc' => $desc ?? $product->desc,
                'price' => $price ?? $product->price
            ]);


            // dd($product);

            $response = [
                'message' => 'data updated successfully',
                'success' => true,
                'newData' => $product,
                'status' => 200
            ];



            return response()->json($response, 200);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'message' => 'product not found to delete',
                'success' => false,
                'status' => 404
            ] , 200);
        }

        else {

            $product->delete();

            $response = [
                'message' => 'product deleted successfully',
                'success' => true,
                'status' => 204
            ];

            return response()->json($response , 200);
        }
    }
}
