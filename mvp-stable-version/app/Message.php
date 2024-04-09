<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable = ['sender_id', 'receiver_id', 'chat_id', 'content'];
    public function chat()
    {
        return $this->belongsTo(Chat::class);
    }
}
