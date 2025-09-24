🤖 ChatGPT Laravel + Vue.js (Vite)

A step-by-step starter project to integrate:

Laravel (Backend)

Vue 3 + Vite (Frontend)

OpenAI PHP Client for ChatGPT

This README is written in detail for freshers so anyone can set it up — regardless of platform (Mac, Windows, Linux, MAMP/XAMPP).


📋 Prerequisites

Make sure you have these installed:

PHP >= 8.2 → check php -v

Composer → check composer -v

Node.js >= 18 and npm → check node -v && npm -v

MAMP/XAMPP (optional, if not using php artisan serve)

OpenAI API Key → get one from OpenAI Platform


🚀 Installation Steps

1. Clone the project

git clone https://github.com/your-username/chatgpt-laravel-vue.git
cd chatgpt-laravel-vue

2. Install Laravel dependencies

composer install
cp .env.example .env
php artisan key:generate

3. Install Node dependencies

npm install

👉 Note:

You do NOT need to install each package separately (vite, axios, tailwind, openai-php/client).

Since they are already listed in composer.json and package.json, running composer install and npm install will install everything automatically.

Only if you’re starting a new empty Laravel project from scratch would you run:

composer require openai-php/client
npm install vue @vitejs/plugin-vue tailwindcss axios


⚙️ Configure Environment

Open .env and add your OpenAI API key:

OPENAI_API_KEY=sk-your_dummy_key_here


📦 Laravel Backend Setup

1. Route (routes/api.php)

<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChatController;

Route::post('/chat', [ChatController::class, 'chat']);


2. Controller (app/Http/Controllers/ChatController.php)

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use OpenAI;

class ChatController extends Controller
{
    public function chat(Request $request)
    {
        $client = OpenAI::client(env('OPENAI_API_KEY'));

        $result = $client->chat()->create([
            'model' => 'gpt-4o-mini', // or gpt-3.5-turbo
            'messages' => [
                ['role' => 'user', 'content' => $request->message],
            ],
        ]);

        return response()->json([
            'reply' => $result->choices[0]->message->content,
        ]);
    }
}

✅ Test OpenAI Connection

Add in routes/web.php:


<?php

use Illuminate\Support\Facades\Route;

Route::get('/', fn () => view('welcome'));

Route::get('/ping', fn () => response()->json(['message' => 'pong']));

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


👉 Test in browser:

http://127.0.0.1:8000/ping → { "message": "pong" }

http://127.0.0.1:8000/check-openai → ChatGPT should reply 🎉



🎨 Vue.js Frontend Setup

resources/js/components/ChatApp.vue

<template>
  <div class="flex items-center justify-center min-h-screen bg-gray-100 p-6">
    <div class="w-full max-w-4xl bg-white shadow-2xl rounded-2xl overflow-hidden flex flex-col">
      <!-- Header -->
      <div class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white p-4 text-lg font-semibold">
        🤖 Chat with AI
      </div>

      <!-- Chat Messages -->
      <div class="flex-1 p-4 space-y-4 overflow-y-auto" ref="chatWindow">
        <div v-for="(msg, index) in chatHistory" :key="index"
             class="flex" :class="msg.sender === 'user' ? 'justify-end' : 'justify-start'">
          <div :class="[
              'px-4 py-2 rounded-lg max-w-full break-words',
              msg.sender === 'user'
                ? 'bg-indigo-500 text-white rounded-br-none'
                : 'bg-gray-200 text-gray-800 rounded-bl-none'
            ]">
            {{ msg.text }}
          </div>
        </div>

        <!-- Typing -->
        <div v-if="loading" class="flex justify-start">
          <div class="bg-gray-200 text-gray-800 px-4 py-2 rounded-lg animate-pulse">
            Typing...
          </div>
        </div>
      </div>

      <!-- Input -->
      <div class="border-t bg-gray-50 p-4 flex items-center gap-2">
        <textarea v-model="message" @keyup.enter.exact.prevent="sendMessage"
                  placeholder="Type your message..."
                  class="flex-1 border border-gray-300 rounded-lg px-3 py-2
                         focus:outline-none focus:ring-2 focus:ring-indigo-500 resize-none"
                  rows="1"></textarea>
        <button @click="sendMessage" :disabled="loading || !message.trim()"
                class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2 rounded-lg shadow
                       transition disabled:opacity-50">
          Send
        </button>
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";

export default {
  data() {
    return { message: "", chatHistory: [], loading: false };
  },
  methods: {
    async sendMessage() {
      if (!this.message.trim() || this.loading) return;
      const userMessage = this.message.trim();
      this.chatHistory.push({ sender: "user", text: userMessage });
      this.message = "";
      this.loading = true;

      try {
        const res = await axios.post("/api/chat", { message: userMessage });
        this.chatHistory.push({ sender: "ai", text: res.data.reply });
      } catch (error) {
        this.chatHistory.push({ sender: "ai", text: "⚠️ Error: Unable to fetch AI response." });
      } finally {
        this.loading = false;
        this.$nextTick(() => {
          this.$refs.chatWindow.scrollTop = this.$refs.chatWindow.scrollHeight;
        });
      }
    },
  },
};
</script>

▶ Running the Project

php artisan serve
npm run dev

Backend → http://127.0.0.1:8000

Frontend injected via Vite


Option B — MAMP/XAMPP (htdocs)

Place project inside /htdocs/chatgpt-app

Run npm run dev

Visit → http://localhost:8888/chatgpt-app/public

📂 .gitignore

/vendor
/node_modules
/public/build
.env

📌 OpenAI Free Account Limits

Free OpenAI accounts allow only a few requests/minute.

If you hit the limit, you’ll see:

"message": "Request rate limit has been exceeded."

✅ Summary

Clone repo

Run composer install & npm install (all deps auto-installed 🎉)

Configure .env with API key

Setup routes, controller, Vue component

Run php artisan serve + npm run dev

Test /ping & /check-openai

Chat with ChatGPT 🎉


🏗 Architecture Diagram

                🌐 Browser (Frontend)
        ┌─────────────────────────────────┐
        │   Vue 3 + Vite + Tailwind CSS    │
        │   - Chat UI (ChatApp.vue)        │
        │   - Axios → API request          │
        └───────────────┬─────────────────┘
                        │  (HTTP POST /api/chat)
                        ▼
        ┌─────────────────────────────────┐
        │   Laravel (Backend, PHP)         │
        │   - routes/api.php → /chat       │
        │   - ChatController.php           │
        │   - Uses openai-php/client       │
        └───────────────┬─────────────────┘
                        │  (API request with key)
                        ▼
        ┌─────────────────────────────────┐
        │   OpenAI API (Cloud)             │
        │   - Model: GPT-4o-mini / GPT-3.5 │
        │   - Processes request            │
        │   - Returns AI reply             │
        └─────────────────────────────────┘
                        ▲
                        │  (JSON response)
        ┌───────────────┴─────────────────┐
        │   Laravel sends reply back       │
        │   Vue updates chat window        │
        └─────────────────────────────────┘


🔄 Flow in Simple Words

User types a message in Vue.js chat box.

Vue sends it via Axios → Laravel API /chat.

Laravel’s ChatController calls OpenAI PHP Client with your API key.

OpenAI processes the request and returns a response.

Laravel sends that response back as JSON.

Vue updates the chat UI with AI’s reply.