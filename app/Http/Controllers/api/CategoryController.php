<?php

namespace App\Http\Controllers\Api;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Container\Attributes\Auth;
use Illuminate\Database\QueryException;

class CategoryController extends Controller
{

    public function index()
    {
        $categories = Category::paginate(10);
        $response = [
            'message' => 'all categories retrieved successfully',
            'data' => $categories,
            'success' => true,
            'status' => 200
        ];

        return response()->json($response , 200);
    }


    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|min:6',

        ]);
        try {

             $newCategory = Category::create([
            'name' => $request->name,
        ]);

        if ($newCategory) {
            $response = [
                'message' => 'Category added successfully',
                'success' => true,
                'status' => 201
            ];

            return response()->json($response , 200);
        }

        } catch (QueryException $e){

            return response()->json([
                'message' => 'failed to add category ' . $request->name . ' is already exist',
                'Error' => $e->getMessage(),
                'success' => false,
                'status' => 500
            ] , 200);

        }




    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $category = Category::find($id);
        if (!$category) {
            $response = [
                'message' => 'sorry this category not exist to show',
                'success' => false,
                'status' => 404
            ];
            return response() -> json($response , 200);
        }else {
            $response = [
                'message' => 'this category is ' . $category->name . ' ',
                'data' => $category,
                'success' => true,
                'status' => 200
            ];
            return response()->json($response , 200);
        }
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
        $request->validate([
            'name' => 'string|min:6',
        ]);

        $category = Category::find($id);

        if(!$category){
            $response = [
                'message' => 'sorry this category not exist to update',
                'success' => false,
                'status' => 404
            ];
            return response()->json($response , 200);
        }else {

      $oldCategory = $category->name;

       $category->name = $request->name ?? $category->name;
       $category->save();

       $newCategory = Category::find($id);

            $response = [
                'message' => 'Category updated from ' . $oldCategory . ' to ' . $category->name,
                'data' => $newCategory,
                'success' => true,
                'status' => 200
            ];
            return response()->json($response , 200);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $category = Category::find($id);

        if(!$category) {
            $response = [
                'message' => 'sorry this category not exist to delete',
                'success' => true,
                'status' => 404
            ];
            return response()->json($response , 200);
        }else {
            $category->delete();

            $response = [
                'message' => 'category is deleted successfully',
                'success' => true,
                'status'=> 204
            ];
            return response()->json($response , 200);
        }
    }
}
