<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
    public $ok = 200;
    public $created = 201;
    public $invalid = 400;
    public $unauthorized = 401;
    public $unallowed = 403;
    public $notFound = 404;

    //success function
    public function success($code, $message, $data = Null,){
            return response()->json([
                'status' => $code,
                'message' => $message,
                'data' => $data,
            ]);
    }

    //error function
    public function error($code, $message, $data = Null){
        return response()->json([
            'status' => $code,
            'message' => $message,
            'data' => $data
        ],$code === 0 ? 400 : $code);
    }
}
