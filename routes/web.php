<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/ping', function () {
    return response()->json(['message' => 'pong']);
});





Route::get('/check-openai', function () {
    $client = OpenAI::client(env('OPENAI_API_KEY'));

    try {
        $response = $client->chat()->create([
            'model' => 'gpt-4o-mini',
            'messages' => [
                ['role' => 'user', 'content' => 'Say hello from Laravel!'],
            ],
        ]);

        return response()->json([
            'status' => 'success',
            'reply' => $response->choices[0]->message->content,
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage(),
        ]);
    }
});