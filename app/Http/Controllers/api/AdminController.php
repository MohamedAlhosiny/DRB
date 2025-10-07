<?php

namespace App\Http\Controllers\Api;

use App\Models\Admin;
use Illuminate\Http\Request;
use App\Http\Requests\adminRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\loginRequestAdmin;

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

            $token = $admin->createToken('tokenAdmin' , ['role:admin'])->plainTextToken;

            $response = [
                'message' => 'Admin login successfully',
                'success' => true,
                'data' => [
                    $admin->name,
                    $admin->email
                ],
                'token' => $token,
                'status' => 200
            ];

            return response()->json($response , 200);

        }

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
}
