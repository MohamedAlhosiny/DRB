<?php

namespace App\Http\Controllers\Api;

use App\Models\Admin;
use Illuminate\Http\Request;
use App\Http\Requests\adminRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\loginRequestAdmin;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;

class AdminController extends Controller
{
    public function register(adminRequest $request) {
        $data_admin = $request->validated();

        $admin = Admin::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);
        if ($admin) {
            $response = [
                'message' => 'admin created successfully',
                'success' => true,
                'data' => $admin->name,
                'status' => 201
            ];

            return response()->json($response , 200);
        }

    }

    public function login(loginRequestAdmin $request){

        $loginRequestAdmin = $request->validated();

        $admin = Admin::where('email' , $request->email)->first();

        if(!$admin || !Hash::check($request->password , $admin->password)) {
            $response = [
                'message' => 'something is error',
                'success' => false ,
                'status' => 401
            ];
            return response()->json($response , 200);
        }else {

            $ability =[ 'role:'.$admin->role] ;

            $token = $admin->createToken('tokenAdmin' , $ability)->plainTextToken;

            $response = [
                'message' => 'Admin login successfully',
                'success' => true,
                'data' => [
                    $admin->name,
                    $admin->email,
                    $admin->role
                ],
                'token' => $token,
                'status' => 200
            ];

            return response()->json($response , 200);

        }

    }

    public function index() {
        $admins = Admin::all(['id' , 'name' , 'email' , 'role' , 'created_at']);


        $response =[
            'message' => 'list of all admins',
            'data' => $admins,
            'success' => true,
            'status' => 200
        ];

        return response()->json($response , 200);
    }

    public function dashboardStats(){
        $stats = [
            'all-users' => User::count(),
            'all-admins' => Admin::count(),
            'all-products' => Product::count(),
            'all-orders' => Order::count(),
            'total-revenue' => Order::where('status' , 'completed')->sum('totalPrice'),
            'top-selling-product' => Product::withCount('orders')
                                        ->orderByDesc('orders_count')
                                        ->first(['id' , 'name' , 'orders_count'])

        ];

        $response = [
            'message' => 'dashboard statistics',
            'data' => $stats,
            'success' => true,
            'status' => 200
        ];

        return response()->json($response , 200);
    }



    public function logout(){
        $admin = Auth::user();

        $admin->currentAccessToken()->delete();

        return response()->json([
            'message' => 'admin logout successfully',
            'success' => true,
            'status' => 200
        ] , 200);
    }




    //for superadmin only
    public function destroy($id) {
        $admin = Admin::find($id);

        if (!$admin) {
            return response()->json([
                'message' => 'Admin not found',
                'success' => false,
                'status' => 404
            ], 200);
        }

        $admin->delete();

        return response()->json([
            'message' => 'Admin deleted successfully',
            'success' => true,
            'status' => 204
        ], 200);
    }


}
