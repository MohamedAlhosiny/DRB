<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\userRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\loginRequestUser;
use App\Traits\ApiResponseTrait;

class UserController extends Controller
{
    use ApiResponseTrait;

public function store(userRequest $request) {


    $data_vilidated = $request->validated();

    $userRegister = User::create([

        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password)
    ]);

    if($userRegister) {

        return $this->successResponse( [
            $userRegister->name ,
            $userRegister->email
        ] , 'Hello user ' . $userRegister -> name . " in our App" , 201);
    } else {

        return $this->errorResponse( null , 'something is not valid' , 422);

    }
}


public function login(loginRequestUser $request ) {

    $userLogin = $request->validated();

    $user = User::where('email' , $request->email)->first();

    if(!$user || !Hash::check($request->password , $user->password) ) {

        return $this->errorResponse( null , 'something is worng' , 401);
    }else {

        $token = $user->createToken('myToken' , ['role:user'])->plainTextToken;


        return $this->successResponse( [
            'name' => $user->name ,
            'email' => $user->email,
            'token' => $token
        ] , 'hello ' . $user->name . ' in our app' , 200);
    }

}

public function logout() {

    $user = Auth::user();

    $user->currentAccessToken()->delete();


    return $this->successResponse( null , 'Logout is done' , 200);
}

public function index() {
    $users = User::all(['id' , 'name' , 'email' , 'created_at']);


    return $this->successResponse( $users , 'All users are here' , 200);
}


public function destroy($id) {
    $user = User::find($id);

    if(!$user) {

        return $this->errorResponse( null , 'User not found' , 404);
    }

    $user->delete();


    return $this->successResponse( null , 'User deleted successfully' , 204);
}



}
