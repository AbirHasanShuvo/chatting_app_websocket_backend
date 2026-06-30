<?php

namespace App\Models;
use App\Models\Chat;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    
    protected $fillable = [
        'sender_id',
        'receiver_id',
        'message',
        'type',
        'chat_id'
    ];

    public function chat(){
        $this->belongsTo(Chat::class);
    }


}
