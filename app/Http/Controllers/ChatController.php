<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use OpenAI;

class ChatController extends Controller
{
    public function chat(Request $request)
    {
        $validated = $request->validate([
            'message' => 'required|string|max:500',
        ]);
        

        // Create OpenAI client
        $client = OpenAI::client(env('OPENAI_API_KEY'));

        // Call GPT model
        $response = $client->chat()->create([
            'model' => 'gpt-4o-mini',
            'messages' => [
                ['role' => 'system', 'content' => 'You are a helpful AI assistant.'],
                ['role' => 'user', 'content' => $validated['message']],
            ],
        ]);

        return response()->json([
            'reply' => $response->choices[0]->message->content,
        ]);
    }
}
