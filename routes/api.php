<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChatController;


/*Route::post('/chat', function (Request $request) {
    $client = OpenAI::client(env('OPENAI_API_KEY'));

    $result = $client->chat()->create([
        'model' => 'gpt-3.5-turbo', // you can upgrade to gpt-4.1-mini
        'messages' => [
            ['role' => 'user', 'content' => $request->message],
        ],
    ]);

    return response()->json([
        'reply' => $result['choices'][0]['message']['content'] ?? "⚠️ No response from AI",
    ]);
});*/



Route::post('/chat', [ChatController::class, 'chat']);