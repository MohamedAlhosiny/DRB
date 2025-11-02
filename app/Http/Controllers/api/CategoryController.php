<?php

namespace App\Http\Controllers\Api;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Container\Attributes\Auth;
use Illuminate\Database\QueryException;
use App\Traits\ApiResponseTrait;

class CategoryController extends Controller
{
    use ApiResponseTrait;

    public function index()
    {
        $categories = Category::paginate(10);
        return $this->successResponse($categories, "all categories retrieved successfully", 200);
    }



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

                return $this->successResponse($newCategory, 'Category added successfully', 201);
            }
        } catch (QueryException $e) {


            return $this->errorResponse(null, 'Failed to add category ' . $request->name . ' is already exist', 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $category = Category::find($id);
        if (!$category) {

            return $this->errorResponse(null , 'sorry this category not exist to show', 404);
        } else {

            return $this->successResponse($category , 'this category is ' . $category->name . '' , 200);
        }
    }




    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'string|min:6',
        ]);

        $category = Category::find($id);

        if (!$category) {

            return $this->errorResponse(null , "sorry this category not exist to update" , 404);
        } else {

            $oldCategory = $category->name;

            $category->name = $request->name ?? $category->name;
            $category->save();

            $newCategory = Category::find($id);


            return $this->successResponse($newCategory , 'Category updated from ' .$oldCategory . ' to ' . $category->name , 200);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $category = Category::find($id);

        if (!$category) {

            return $this->errorResponse( null , 'sorry this category not exist to delete' , 404);
        } else {
            $category->delete();


            return $this->successResponse( null , 'category is deleted successfully' , 204);
        }
    }
}
