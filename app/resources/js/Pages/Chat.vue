<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Markdown from '@/Components/Markdown.vue';
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';
import { router } from '@inertiajs/vue3'
import { watch, useTemplateRef, computed } from 'vue';

const props = defineProps({
  systemResponse: {
    type: String,
  }
});

const chatContextLookback = 10;

const textAreaRef = useTemplateRef('textareaRef');
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
  textAreaRef.value.style.height = 'auto'; // Reset height after sending message
}

const autoGrow = () => {
  textAreaRef.value.style.height = 'auto';
  textAreaRef.value.style.height = textAreaRef.value.scrollHeight + 'px';
}

const sendDisabled = computed(() => {
  return newMessage.value.trim().length < 1;
});

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
                    <Markdown :content="message.content" />
                  </div>
                </div>
              </div>
            </div>

            <div class="p-4 bg-white flex items-center space-x-4">
              <textarea ref="textareaRef" v-model="newMessage" @keyup="autoGrow" @keyup.enter="sendMessage" placeholder="Type your message..."
                class="flex-1 p-2 border border-gray-300 rounded-lg resize-none h-16"></textarea>
              <button @click="sendMessage" class="bg-blue-500 text-white px-4 py-2 rounded-lg disabled:bg-gray-300"
                :disabled="sendDisabled">
                Send
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
