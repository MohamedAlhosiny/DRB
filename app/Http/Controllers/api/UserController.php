<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\userRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{

public function store(userRequest $request) {


    $data_vilidated = $request->validated();

    $userRegister = User::create([

        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password)
    ]);

    if($userRegister) {
        $response = [
            'message' => 'Hello ' . $userRegister -> name . " in our App",
            'data' => [$userRegister->name ,
            $userRegister->email],
            'success' => true,
            'status' => 201
        ];

        return response()->json($response , 200);
    } else {
        $response = [
            'message' => 'something is not valid',
            'success' => false,
            'status' => 422
        ];

        return response()->json($response , 200);

    }
}
}
