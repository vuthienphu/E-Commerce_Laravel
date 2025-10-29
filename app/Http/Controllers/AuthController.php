<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use  \Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except'=>['register','login']]);
    }


    public function register(Request $request)
    {
$validate = $request -> validate(['name'=>'required|string|max:255',
                                 'email'=>'required|email|unique:users,email',
                                 'password'=>'required|string|min:8|confirmed']);
        $user = User::create(['name'=>$validate['name'],
                               'email'=>$validate['email'],
                               'password'=>$validate['password'],
                               'role'=>'user']);
        return response() -> json(['message'=>'Tao tai khoan thanh cong',
                                   'user'=>$user],201);
    }

    public function login(Request $request)
    {
$credentials = $request -> validate(['email'=>'required|email',
                                 'password'=>'required|string|min:8']);
if(!$token = auth('api')->attempt($credentials)){
        return response()->json(['error'=>'Invalid credentials'],401);
    }
$user = auth('api')->user();

 $ttl = JWTAuth::factory()->getTTL() * 60; 

return response()->json([  'access_token'=>$token,
            'token_type'=>'bearer',
            'expires_in' => $ttl,
            'user'=>$user,
            'role'=>$user->role,
]);

    }

   public function refreshToken(Request $request)
    {
        $newToken = JWTAuth::manager()->refresh(
            JWTAuth::getToken()
        );

        return response()->json([
            'access_token' => $newToken,
            'token_type' => 'bearer',
            'expires_in' => JWTAuth::factory()->getTTL() * 60,
        ]);
    }
}
