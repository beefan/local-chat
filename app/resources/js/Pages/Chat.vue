<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';
import { router } from '@inertiajs/vue3'
import { watch } from 'vue';

const props = defineProps({
  systemResponse: {
    type: String,
  }
});

const chatContextLookback = 10;

const newMessage = ref('');
const messages = ref([
  {
    content: 'What do you want to chat about?',
    role: 'assistant'
  }
]);

const sendMessage = () => {
  messages.value.push({ content: newMessage.value, role: 'user' });
  newMessage.value = "";

  router.post('/chat', { messages: messages.value.slice(-chatContextLookback) });
}

watch(() => props.systemResponse, () => {
  messages.value.push({ content: props.systemResponse, role: 'assistant' });
});
</script>

<template>
  <Head title="Chat" />

  <AuthenticatedLayout>
    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="flex flex-col h-[calc(100vh-150px)] bg-gray-200">
            <div class="flex-1 p-4 overflow-y-auto space-y-4">
              <div v-for="message in messages">
                <div :class="'flex ' + (message.role == 'user' ? 'justify-end' : 'justify-start')">
                  <div
                    :class="'p-3 rounded-lg max-w-xs ' + (message.role == 'user' ? 'bg-purple-500 text-white' : 'bg-gray-300 text-black')">
                    <p>{{ message.content }}</p>
                  </div>
                </div>
              </div>
            </div>

            <div class="p-4 bg-white flex items-center space-x-4">
              <input v-model="newMessage" @keyup.enter="sendMessage" type="text" placeholder="Type your message..."
                class="flex-1 p-2 border border-gray-300 rounded-lg" />
              <button @click="sendMessage" class="bg-blue-500 text-white px-4 py-2 rounded-lg disabled:bg-gray-300"
                :disabled="newMessage.trim().length < 1">
                Send
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

