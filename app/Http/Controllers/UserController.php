<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;



use \Illuminate\Database\QueryException;


class UserController extends Controller
{
    public function Create(Request $request){
        $validator = Validator::make($request->all(),[
            'name' => 'required',
            'email' => 'required',
            'password' => 'required|min:6',
            'password_confirmation' => 'required|min:6'
        ]);

        if ($validator -> fails())
            return $validator->errors()->toJson();

        if($request -> post("password") !== $request -> post("password_confirmation"))
            return [ "password" => "Both passwords don't match"];

        try {
            return User::create([
                'name' => $request -> post("name"),
                'email' => $request -> post("email"),
                'password' => Hash::make($request -> post("password"))
            ]);
        }
        catch (QueryException $e){
            return [
                "error" => 'User ' . $request->post("name") . ' exists',
                "trace" => $e -> getMessage()
            ];
        }
    }

    public function Authenticate(Request $request){
        $validator = Validator::make($request->all(),[
            'email' => 'required',
            'password' => 'required|min:6'
        ]);

        if ($validator->fails())
            return $validator->errors()->toJson();

        $credentials = $request->only('email', 'password');

        if(!Auth::attempt($credentials))
            return [
                'Status' => false,
                'Result' => "No Popotico"
            ];
    }
}
