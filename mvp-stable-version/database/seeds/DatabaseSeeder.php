<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB; // Import DB facade
use Illuminate\Support\Str; // Import Str facade for generating random strings
use Illuminate\Support\Facades\Hash; // Import Hash facade for hashing the password

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // Create Users
        $userIds = [];
        for ($i = 0; $i < 10; $i++) {
            $userIds[] = DB::table('users')->insertGetId([
                'name' => Str::random(10),
                'email' => Str::random(10) . '@example.com',
                'password' => Hash::make('password'),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Create Chats and link Users to Chats
        for ($i = 0; $i < 5; $i++) {
            $chatId = DB::table('chats')->insertGetId([
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Randomly select users to be in this chat
            $chatUsers = array_rand(array_flip($userIds), 2); // Select 2 users for the chat
            foreach ($chatUsers as $userId) {
                DB::table('chat_user')->insert([
                    'chat_id' => $chatId,
                    'user_id' => $userId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Create Messages for the chat
            foreach (range(1, 10) as $index) { // 10 messages per chat
                DB::table('messages')->insert([
                    'sender_id' => $chatUsers[array_rand($chatUsers)],
                    'receiver_id' => $chatUsers[array_rand($chatUsers)],
                    'chat_id' => $chatId,
                    'content' => Str::random(100), // Example content
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
