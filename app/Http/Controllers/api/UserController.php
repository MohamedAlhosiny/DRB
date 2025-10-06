<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\userRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\loginRequestUser;

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


public function login(loginRequestUser $request ) {

    $userLogin = $request->validated();

    $user = User::where('email' , $request->email)->first();

    if(!$user || !Hash::check($request->password , $user->password) ) {
        $response = [
            'message' => 'something is worng',
            'success' => false ,
            'status' => 401
        ];

        return response()->json($response , 200);
    }else {

        $token = $user->createToken('myToken' , ['role:user'])->plainTextToken;

        $response =  [
            'message'=> 'hello ' . $user->name . ' in our app',
            'data'=> [
                $user->name ,
                $user->email
            ],
            'success' => true,
            'token' => $token,
            'status' => 200
        ];

        return response()->json($response , 200);
    }

}

public function logout() {

    $user = Auth::user();

    $user->currentAccessToken()->delete();

    $response = [
        'message' => 'Logout is done',
        'success' => true,
        'status' => 200
    ];
    return response()->json($response , 200);
}


}
