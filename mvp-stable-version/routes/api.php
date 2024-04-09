<?php

use Illuminate\Http\Request;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\MessageController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);

Route::post('messages', [ChatController::class, 'message']);

Route::middleware('auth:api')->group(function () {
    // Fetch chat histories for a user
    Route::get('/chats', 'ChatController@index');
    // Fetch messages for a specific chat
    Route::get('/chats/{chat}', 'ChatController@show');
    // Create a new chat
    Route::post('/chats', 'ChatController@store');
    // Send a message within a chat
    Route::post('/chats/{chat}/messages', 'MessageController@store');
});
Route::middleware('auth:api')->group(function () {
    Route::get('/chats/{chat}/messages', [MessageController::class, 'fetchMessages']);
    Route::post('/chats/{chat}/messages', [MessageController::class, 'sendMessage']);
});


