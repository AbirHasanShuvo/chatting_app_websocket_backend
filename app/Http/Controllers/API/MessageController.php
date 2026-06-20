<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function getUsers()
    {
        // return auth()->user();
        $users = User::where('id', '!=', auth()->user()->id)->get();

        return response()->json([
            'users' => $users,
        ]);
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'message' => 'required',
            'type' => 'required|in:text,video,photo',
        ]);

        Message::create([
            'receiver_id' => $request->receiver_id,
            'sernder_id' => auth()->user()->id,
            'message' => $request->message,
            'type' => $request->type,

        ]);

        return response()->json([
            'message' => 'Message sent',
        ]);
    }
}
