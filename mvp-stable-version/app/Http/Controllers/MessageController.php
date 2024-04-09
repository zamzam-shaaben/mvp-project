<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Chat; // Adjust this path based on the actual location of your Chat model
use App\Events\MessageSent; // Adjust this path based on the actual location of your MessageSent event
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{


    public function store(Request $request, Chat $chat)
    {
        $this->authorize('send', $chat);
        $request->validate(['content' => 'required|string']);

        $message = $chat->messages()->create([
            'sender_id' => auth()->id(),
            'content' => $request->content
        ]);

        broadcast(new MessageSent($message))->toOthers();

        return response()->json($message, 201);
    }

    public function fetchMessages(Chat $chat)
    {
        // Eager load messages for the specified chat
        $messages = $chat->messages()->get();

        // Return the messages as a JSON response
        return response()->json($messages);
    }

    public function sendMessage(Request $request, Chat $chat){
        // Access request data using $request->input('key') or $request->get('key')
        $content = $request->input('content'); // Assuming the request contains a 'content' field

        // Perform validation if necessary
        $validated = $request->validate([
            'content' => 'required|string|max:1000', // Example validation rules
            // Add other fields and rules as necessary
        ]);

        // Use the $chat object and the validated data to create a new message
        $message = $chat->messages()->create([
            'sender_id' => auth()->id(), // Example: setting the sender_id to the current user's ID
            'content' => $validated['content'],
            'receiver_id' => $request->input('receiver_id'),
            'created_at' => $request->input('sent_at')
        ]);

        // Optionally, return the created message or a success response
        return response()->json($message, 201);
    }
}
