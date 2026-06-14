<?php

namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    public function register(Request $request)
    {
        // $request->validate([
        //     'name' => 'required',
        //     'date_of_birth' => 'required',
        //     'email' => 'required|unique:users,email',
        //     'phone' => 'required|unique:users,phone',
        //     'password' => 'required',
        //     'profile_picture' => 'required|image'
        // ]);

        $file_path = $request->profile_picture->store('/profile_picture', 'public');

        // User::create([
        //     'name' => $request->name,
        //     'date_of_birth' => $request->date_of_birth,
        //     'email' => $request->email,
        //     'phone' => $request->phone,
        //     'password' => bcrypt($request->password)
        // ]);
        return $file_path;


    }
}
