<template>
  <div class="flex items-center justify-center min-h-screen bg-gray-100 p-6">
    <div class="w-full max-w-6xl mx-auto bg-white shadow-2xl rounded-2xl overflow-hidden flex flex-col">
      <!-- Header -->
      <div class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white p-4 text-lg font-semibold">
        🤖 Chat with AI
      </div>

      <!-- Messages -->
      <div class="flex-1 p-4 space-y-4 overflow-y-auto" ref="chatWindow">
        <div
          v-for="(msg, index) in chatHistory"
          :key="index"
          class="flex"
          :class="msg.sender === 'user' ? 'justify-end' : 'justify-start'"
        >
          <div
            :class="[
              'px-4 py-2 rounded-lg max-w-3xl break-words',
              msg.sender === 'user'
                ? 'bg-indigo-500 text-white rounded-br-none'
                : 'bg-gray-200 text-gray-800 rounded-bl-none'
            ]"
          >
            {{ msg.text }}
          </div>
        </div>

        <!-- Typing indicator -->
        <div v-if="loading" class="flex justify-start">
          <div class="bg-gray-200 text-gray-800 px-4 py-2 rounded-lg rounded-bl-none animate-pulse">
            Typing...
          </div>
        </div>
      </div>

      <!-- Input -->
      <div class="border-t bg-gray-50 p-4 flex items-center gap-2">
        <textarea
          v-model="message"
          @keyup.enter.exact.prevent="sendMessage"
          placeholder="Type your message..."
          class="flex-1 border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 resize-none"
          rows="1"
        ></textarea>
        <button
          @click="sendMessage"
          :disabled="loading || !message.trim()"
          class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2 rounded-lg shadow transition disabled:opacity-50"
        >
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
    return {
      message: "",
      chatHistory: [],
      loading: false,
    };
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
        this.chatHistory.push({
          sender: "ai",
          text: "⚠️ Error: Unable to fetch AI response.",
        });
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

<style>
/* Custom scrollbar for chat */
.flex-1::-webkit-scrollbar {
  width: 6px;
}
.flex-1::-webkit-scrollbar-thumb {
  background: rgba(100, 116, 139, 0.5); /* slate-500 */
  border-radius: 10px;
}
</style>
