<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        //validattion 
        $validattion = Validator::make(
            $request->all(),
            [
                'first_name' => 'required',
                'last_name' => 'required',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|min:8|max:20',
                'c_password' => 'required|same:password',
                'birth_date' => 'required|date',
                'profile_photo' => 'image',
                'gender' => 'required|in:male,female',
                'phone_number' => 'required'
            ]
        );

        // return an error message if some error occures
        if ($validattion->fails()) {
            return response()->json([
                "message" => $validattion->errors()
            ], 400);
        }

        $input = $request->all();
        $input['password'] = bcrypt($request->password);
        if ($request->file('profile_photo') == null) {
            $input['profile_photo'] = "/upload/photos/profile_photos/default_profile_photo.png";
        } else {
            $photo = $request->file('profile_photo');
            $newPhoto = time() . $photo->getClientOriginalName();
            $photo->move('upload/photos/profile_photos/', $newPhoto);
            $input['profile_photo'] = "/upload/photos/profile_photos/$newPhoto";
        }


        $user = User::create($input);
        $token = $user->createToken(")AKRAM(")->accessToken;

        return response()->json([
            "message" => "registered successfully",
            "token" => $token,
            "user" => $user
        ]);
    }

    public function login(Request $request)
    {
        //validattion 
        $validattion = Validator::make(
            $request->all(),
            [
                'email' => 'required|email|exists:users,email',
                'password' => 'required|min:8|max:20'
            ]
        );
        if ($validattion->fails()) {
            return response()->json([
                "message" => $validattion->errors()
            ], 400);
        }
        if (!auth()->attempt($request->all())) {
            return response()->json([
                "message" => "incorrect password for this email"
            ], 401);
        }
        //create access token 
        $token = auth()->user()->createToken(")AKRAM(")->accessToken;

        return response()->json([
            "message" => "logged in successfully",
            "token" => $token,
            "user" => auth()->user()
        ]);
    }

    public function logout(Request $request)
    {
        $token = auth()->user()->token();
        $token->revoke();
        return response()->json([
            "message" => "logged out successfully",
        ]);
    }
}
