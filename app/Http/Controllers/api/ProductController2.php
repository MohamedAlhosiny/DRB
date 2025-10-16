<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\productRequest;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Models\Product;
use Dotenv\Util\Str;

class ProductController2 extends Controller
{

    public function index() {

        // $products = Product::paginate(5);
        $products = Product::with('category')->get();
        $response = [
            'message' => 'all products retrieved successfully',
            'success' => true ,
            'data' => $products,
            'status' => 200
        ];
        return response()->json($response , 200);

    }


    public function show(string $id){

        $product = Product::find($id);

        if (!$product) {
            $response = [
                'message' => 'sorry this product not found to show' ,
                'success' => false ,
                'status' => 404
            ];
            return response()->json($response , 200);
        }else {


            $response = [
                'message' => 'this product is ' . $product->name,
                'success' => true,
                'data' => $product,
                'status' => 200
            ];

            return response()->json($response , 200);

        }

    }

    public function store(productRequest $request){
        $productValidate = $request->validated();

        $category = Category::find($request->category_id);

        if(!$category) {
            return response()->json([
                'message' => 'category not found to select',
                'success' => false ,
                'status' => 404
            ] , 200);
        }
        
            $product = Product::create([
                'name' => $request->name,
                'description' => $request->description,
                'price' => $request->price,
                'category_id' => $request->category_id,
                // 'status' => $request->status ?? false
            ]);

            if ($product) {

                $productWithCategory = Product::withAggregate('category' , 'name')->find($product->id);

                $response = [
                    'message' => 'product stored successfully',
                    'success' => true,
                    'data' => $productWithCategory,
                    'status' => 201
                ];

                return response()->json($response , 200);

            }

    }

    public function changeStatus(string $id) {
        $product = Product::find($id);

        $oldStatus = $product->status;

        if($product->status == 'unactive') {
            $product->status = 'active';
        } else {
            $product->status = 'unactive';
        }

        $product->save();

        $productAfterUpdated = Product::find($id);
        $response = [
            'message' => 'status is changed successfully from ' . $oldStatus . ' to ' . $product->status,
            'success' => true ,
            'data' => $productAfterUpdated,
            'status' => 200
        ];

        return response()->json($response , 200);
    }


    public function update (Request $request , string $id) {

        $product = Product::find($id);

        $oldProduct = Product::where('id' , $id)->withAggregate('category' , 'name')->get(['name' , 'description' , 'price' , 'category_id']);
        if (!$product){
            return response()->json([
                'message' => 'this product not found to update',
                'success' => false ,
                'status' => 404
            ], 200);
        }

        $request->validate([
            'name' => 'string|min:3',
            'description' => 'nullable|min:5|string',
            'price' => 'numeric',
            'category_id' => 'exists:categories,id'
        ]);

        //   if($request->has('category_id')){

        //         $category = Category::find($request->category_id);

        //         if(!$category) {
        //             return response()->json([
        //                 'message' => 'this category not found to related with product' ,
        //                 'success' => false ,
        //                 'status' => 404
        //             ] , 200);
        //         }

        //     }

        $product->update([
            'name' => $request->name ?? $product->name,
            'description' => $request->description ?? $product->description,
            'price' => $request->price ?? $product->price,
            'category_id' => $request->category_id ?? $product->category_id
        ]);
        $product = Product::find($id);

        $productUpdated = Product::withAggregate('category' , 'name')->find($request->id);

        $response = [
            'message' => 'product updated successfully',
            'success' => true,
            'data' => [
                'oldData' => $oldProduct,
                'newData' => $productUpdated
            ],
            'status' => 200
        ];

        return response()->json($response , 200);


    }

    public function destroy(string $id){
        $product = Product::find($id);
        // dd($product);
        if(!$product){
            return response()->json([
                'message' => 'this product not found to delete',
                'success' => false,
                'status' => 404
            ] , 200);
        }else {

            $product->delete();

            $response = [
                'message' => 'this product deleted successfully',
                'success' => true ,
                'status' => 204
            ];

            return response() -> json($response , 200);
        }
    }
}
