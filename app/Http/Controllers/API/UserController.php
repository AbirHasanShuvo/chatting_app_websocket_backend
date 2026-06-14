<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;

class UserController extends Controller
{
    public function register()
    {
        return response()->json([
            'status' => 'Successfully you have run your API',
        ]);
    }
}
