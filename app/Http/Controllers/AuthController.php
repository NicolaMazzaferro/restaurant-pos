<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller {
    // POST /api/auth/login -> {email,password}
    public function login(Request $request) {
        $request->validate(['email'=>'required|email','password'=>'required']);
        $user = User::where('email',$request->email)->first();
        if(!$user || !Hash::check($request->password,$user->password)){
            return response()->json(['message'=>'Invalid credentials'], 401);
        }
        $token = $user->createToken('pos-token')->plainTextToken;
        return response()->json(['token'=>$token,'token_type'=>'Bearer']);
    }
}
