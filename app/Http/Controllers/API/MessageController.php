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

    public function sendMessages(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'message' => 'required',
            'type' => 'required|in:text,video,photo',
        ]);

        $message = Message::create([
            'receiver_id' => $request->receiver_id,
            'sender_id' => auth()->id(),
            'message' => $request->message,
            'type' => $request->type,
        ]);

        $message->is_me = true; // sender is always the auth user

        return response()->json($message); // return the full message object
    }

    public function getMessages($id)
    {
        $user = auth()->user();
        $messages = Message::where(function ($query) use ($user, $id) {
            $query->where('sender_id', $user->id)
                ->where('receiver_id', $id);
        })->orWhere(function ($query) use ($user, $id) {
            $query->where('sender_id', $id)
                ->where('receiver_id', $user->id);
        })
            ->orderBy('created_at', 'asc')
            ->get();

        $messages = $messages->map(function ($message) use ($user) {
            $message->is_me = $message->sender_id == $user->id;

            return $message;
        });

        return response()->json($messages);
    }
}
