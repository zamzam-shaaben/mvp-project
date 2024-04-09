<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{

    public function messages()
    {
        return $this->hasMany(Message::class, 'chat_id');
    }

    public function users()
    {
        return $this->belongsToMany('App\User', 'chat_user', 'chat_id', 'user_id');
    }
}
