<?php

namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
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

        return response()->json([
            'status' => 'Success',
            'data' => $user
        ]);



    }
}
