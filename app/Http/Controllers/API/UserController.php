<?php

namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'date_of_birth' => 'required',
            'email' => 'required|unique:users,email',
            'phone' => 'required|unique:users,phone',
            'password' => 'required',
            'profile_picture' => 'required|image'
        ]);

        $file_path = $request->profile_picture->store('/profile_picture', 'public');
        $profilePicture = Storage::url($file_path);

        $user = User::create([
            'name' => $request->name,
            'date_of_birth' => $request->date_of_birth,
            'email' => $request->email,
            'phone' => $request->phone,
            'profile_picture' => $profilePicture,
            'password' => bcrypt($request->password)
        ]);

        $token = $user->createToken('auth')->plainTextToken;

        return response()->json([
            'status' => 'Success',
            'token' => $token,
            'data' => $user,
        ]);



    }

    public function login(Request $request){
        $request->validate([
            'email' => 'required|exists:users,email',
            'password' => 'required'
        ]);

        if(Auth::attempt($request->only('email', 'password'))){
            $user = User::where('email', $request->email)->first();
            $token = $user->createToken('auth')->plainTextToken;

            return response()->json([
            'message' => 'User logged in',
            'token' =>  $token,
            'data' => $user

        ]);
        }

        else{
            return response()->json(['message'=>'Invalid details']);
        }

        
    }
}
