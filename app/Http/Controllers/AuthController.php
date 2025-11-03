<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Routing\Controller;
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
 JWTAuth::factory()->setTTL(3); //token sống được 5 phút
if(!$token = auth('api')->attempt($credentials)){
        return response()->json(['error'=>'Email hoặc mật khẩu không đúng'],401);
    }
$user = auth('api')->user();

 

return response()->json(['access_token'=>$token,
            'token_type'=>'bearer',
             'token_type' => 'bearer',
        'expires_in' => JWTAuth::factory()->getTTL() * 60, // 15 phút = 900 giây
            ]);

}

   public function refreshToken(Request $request)
    {
        try {
        $token = JWTAuth::parseToken()->refresh(); // tự lấy token từ header
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => JWTAuth::factory()->getTTL() * 60,
        ]);
    } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
        return response()->json(['error' => 'Token đã hết hạn, vui lòng đăng nhập lại'], 401);
    } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
        return response()->json(['error' => 'Không thể refresh token'], 400);
    }
    }
}
