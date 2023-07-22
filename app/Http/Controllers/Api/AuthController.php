<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use GuzzleHttp\Psr7\Response;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response([
                'success' => 404,
                'message' => "The provided credentials are incorrect.",
            ]);
        }
        $user->tokens()->delete();

        $token = $user->createToken($request->email)->plainTextToken;

        return response([
            'success' => 200,
            'token' => $token,
        ]);
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'phone' => 'required',
            'email' => 'required|email',
            'password' => 'required',
        ]);
        $user = User::all()->count();
        if($user == 0){
            $user = new  User();
            $user->name =  $request->name;
            $user->phone =  $request->phone;
            $user->email =  $request->email;
            $user->password = Hash::make($request->password);
            $user->save();

            $token = $user->createToken($user->email)->plainTextToken;
        } else{
            return response()->json(['success' => $this->unallowed, 'error' => "Unable to register"]);
        }

        return response([
            'user' => $user,
            'token' => $token,
        ]);
    }
}
