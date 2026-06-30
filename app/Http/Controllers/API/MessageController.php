<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Chat;
use App\Models\Message;
use App\Models\User;
use App\Events\MessageSent;
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

        $chat = Chat::where(function ($query) use ($request) {
            $query->where('sender_id', auth()->id())
                ->where('receiver_id', $request->receiver_id);
        })->orWhere(function ($query) use ($request) {
            $query->where('sender_id', $request->receiver_id) // Note: fixed variable name here too
                ->where('receiver_id', auth()->id());
        })->first();

        //if the chati is wmpty then needed to create it first
        if(empty($chat)){
            $chat = Chat::create([
                'sender_id' => auth()->id(),
                'receiver_id' => $request->receiver_id
            ]);
        }

        $message = Message::create([
            'chat_id' => $chat->id,
            'receiver_id' => $request->receiver_id,
            'sender_id' => auth()->id(),
            'message' => $request->message,
            'type' => $request->type,
        ]);

        //for broadcasting 
        broadcast(new MessageSent($message))->toOthers();

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
