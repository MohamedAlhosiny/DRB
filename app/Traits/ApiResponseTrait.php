<?php

namespace App\Traits;

trait ApiResponseTrait
{
    protected function successResponse($data = null , $message = 'success' , $status = null) {
        return response()->json([
            'message' => $message,
            'data' => $data,
            'success' => true,
            'status' => $status
        ] ,  200);

    }

    protected function errorResponse($data = null , $message = 'error' , $status = null){
        return response()->json([
            'message' => $message,
            'data'=>$data,
            'success' => false,
            'status'=>$status
        ] , 200);
    }
}
