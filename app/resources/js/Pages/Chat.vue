<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Markdown from '@/Components/Markdown.vue';
import ChatHistory from '@/Components/Chat/HistorySidebar.vue';
import { Head } from '@inertiajs/vue3';
import { useTemplateRef, computed, toRef, ref, onMounted, provide, nextTick } from 'vue';

const props = defineProps({
  messageHistory: {
    type: Array,
    required: false,
  }, 
  chatHistory: {
    type: Array,
    required: false,
  },
  title: {
    type: String,
    required: false,
  },
  id: {
    type: Number,
    required: false,
  },
  prioritizeHistory: {
    type: Boolean,
    required: false,
    default: false,
  },
});

const chatId = toRef(props.id ?? null);
const chatTitle = toRef(props.title ?? 'Untitled Chat');
const messages = toRef(props.messageHistory ?? [
  {
    content: 'What do you want to chat about?',
    role: 'assistant'
  }
]);
const chatHistory = toRef(props.chatHistory ?? []);

const chatContainerRef = useTemplateRef('chatContainerRef');
const textAreaRef = useTemplateRef('textareaRef');
const newMessage = ref('');

const sendLoading = ref(false);
const sendMessage = async () => {
  sendLoading.value = true;

  const userMessage = { content: newMessage.value, role: 'user' };
  messages.value.push(userMessage);

  scrollToBottom();

  const response = await axios.post('/chat', { 
    messages: [userMessage], 
    chatId: chatId.value
  });

  messages.value.push({ content: response.data.systemResponse, role: 'assistant' });
  if (! chatId.value) {
    chatHistory.value.unshift(response.data.chat);
  }
  chatId.value = response.data.chat.id;
  chatTitle.value = response.data.chat.title;

  newMessage.value = '';
  textAreaRef.value.style.height = 'auto';
  
  nextTick(() => {
    scrollToBottom();
  });
  
  sendLoading.value = false;
};

const sendDisabled = computed(() => {
  return sendLoading.value || newMessage.value.trim().length < 1;
});

const screenIsSmall = ref(computed(() => window.innerWidth < 640));

const chatBubbleStyle = (role) => {
  return 'p-3 rounded-lg max-w-xs ' + (role == 'user' ? 'bg-purple-500 text-white' : 'bg-gray-300 text-black');
}
const chatBubbleContainerStyle = (role) => {
  return 'flex px-3 ' + (role == 'user' ? 'justify-end' : 'justify-start');
}
const mainChatStyle = computed(() => {
  return screenIsSmall.value ? 'mx-auto w-full' : 'mx-4 w-3/4';
});

const autoGrow = () => {
  textAreaRef.value.style.height = 'auto';
  textAreaRef.value.style.height = textAreaRef.value.scrollHeight + 'px';
}
const scrollToBottom = () => {
  if (chatContainerRef.value) {
    chatContainerRef.value.lastElementChild.scrollIntoView({ behavior: 'smooth' });
  }
}

provide('screenIsSmall', screenIsSmall.value);
provide('prioritizeHistory', props.prioritizeHistory);

onMounted(() => {
  scrollToBottom();
});
</script>

<template>
  <Head title="Chat" />

  <AuthenticatedLayout>
    <h1 v-if="! props.prioritizeHistory" class="flex justify-center items-center pt-2 text-lg">{{ chatTitle }}</h1>    
    <div class='py-6 flex'>
      <ChatHistory :chats="chatHistory" :current="chatId"/>
      
      <div :class="mainChatStyle" v-if="!props.prioritizeHistory">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="flex flex-col h-[calc(100vh-150px)] bg-gray-200">

            <!-- Chat Container-->
            <div ref="chatContainerRef" class="flex-1 p-4 overflow-y-auto space-y-4">
              <div v-for="(message, index) in messages" :key="index">
                <div :class="chatBubbleContainerStyle(message.role)">
                  <div
                    :class="chatBubbleStyle(message.role)">
                    <Markdown :content="message.content" />
                  </div>
                </div>
              </div>
            </div>

            <!-- Chat Input -->
            <div class="p-4 bg-white flex items-center space-x-2">
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
