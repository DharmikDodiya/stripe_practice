<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request){
        $request->validate([
            'name'                  => 'required|string',
            'email'                 => 'required|unique:users,email|email',
            'password'              => 'required|same:password_confirmation|min:6|max:12',
            'password_confirmation' => 'required',
        ]);

        $user = User::create($request->only(['name','email'])
        +[
            'token'     => Str::random(64),
            'password'  => Hash::make($request->password),
        ]
        );
        return response()->json('User Data',$user);
    }

    /**
    * user Login
    */

     public function login(Request $request){
        $request->validate([
            'email'=>'required',
            'password'=>'required'
        ]);
        //dd($request->all());
        if(Auth::attempt(['email' =>$request->email,'password' => $request->password ])){
            $user = User::where('email', $request->email)->first();

            return response()->json(['You Are Login Now',$user->createToken("API TOKEN")->plainTextToken,200]);
        }
            return response()->json('Invalid Email Or Password');
        }
}
