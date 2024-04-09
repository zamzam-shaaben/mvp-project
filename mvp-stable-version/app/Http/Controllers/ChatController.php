<?php

namespace App\Http\Controllers;
use App\Events\MessageSent;
use App\Chat;
use App\Message;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
class ChatController extends Controller
{
    public function index()
    {

        //$user = Auth::user();

        $userId = auth()->id(); // or Auth::id();

        // Re-query the user model from the database to get the latest state,
        // including eager loading the 'chats' relationship
        $lastMessagesSub = DB::table('messages')
            ->select('chat_id', DB::raw('MAX(id) as last_message_id'))
            ->groupBy('chat_id');
        $user = DB::table('users as u')
                ->join('chat_user as cu', 'u.id', '=', 'cu.user_id')
                ->join('chat_user as cu2', function ($join) {
                    $join->on('cu.chat_id', '=', 'cu2.chat_id')
                        ->on('cu.user_id', '<>', 'cu2.user_id');
                })
                ->join('users as u2', 'cu2.user_id', '=', 'u2.id')
                ->joinSub($lastMessagesSub, 'last_messages', function ($join) {
                    $join->on('cu.chat_id', '=', 'last_messages.chat_id');
                })
                ->join('messages as m', 'm.id', '=', 'last_messages.last_message_id')
                ->where('u.id', $userId)
                ->distinct()
                ->select(
                    'cu.chat_id as chat_id',
                    'u.name as user_name',
                    'u.email as user_email',
                    'u2.name as other_user_name',
                    'u2.email as other_user_email',
                    'm.content as last_message_content',
                    'm.created_at as last_message_time'
                )
        ->get();

        // Load the chats along with the latest message and all users associated with each chat
        // $chats = $user->chats()->with(['messages' => function($query) {
        //     $query->latest()->first();
        // }, 'users'])->get();

        return response()->json($user);
    }

    public function show(Chat $chat)
    {
        $this->authorize('view', $chat);

        // Load the chat's messages and the users associated with each message
        $messages = $chat->messages()->with('user')->get();

        return response()->json(['chat' => $chat, 'messages' => $messages]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
        ]);

        // Create the chat
        $chat = Chat::create();

        // Attach the authenticated user and the other user(s) to the chat
        $chat->users()->attach(array_merge($request->user_ids, [Auth::id()]));

        return response()->json($chat, 201);
    }
}
