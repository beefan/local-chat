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
  systemPromptId: {
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

  newMessage.value = '';
  textAreaRef.value.style.height = 'auto';

  await nextTick();
  scrollToBottom();

  const response = await axios.post('/chat', { 
    messages: [userMessage], 
    chatId: chatId.value,
    systemPromptId: selectedSystemPrompt.value
  });

  messages.value.push({ content: response.data.systemResponse, role: 'assistant' });
  if (! chatId.value) {
    chatHistory.value.unshift(response.data.chat);
  }
  chatId.value = response.data.chat.id;
  chatTitle.value = response.data.chat.title;
  
  await nextTick();
  scrollToBottom();
  
  sendLoading.value = false;
};

const sendDisabled = computed(() => {
  return sendLoading.value || newMessage.value.trim().length < 1;
});

const systemPromptDropDown = ref(false);
const selectedSystemPrompt = ref(null);
const systemPrompts = ref([]);

const toggleSystemPromptDropDown = () => {
  systemPromptDropDown.value = !systemPromptDropDown.value;
};
const applyPrompt = async () => {
  if (! chatId.value || ! selectedSystemPrompt.value) {
    return;
  }

  await axios.patch(`/chat/${chatId.value}`, { 
    systemPromptId: selectedSystemPrompt.value
  });
};

const loadSystemPrompts = async () => {
  const response = await axios.get('/system-prompts/get');
  systemPrompts.value = response.data;
  
  if (props.systemPromptId) {
    selectedSystemPrompt.value = systemPrompts.value.find(p => p.id === props.systemPromptId)?.id;
  } else 
  {
    selectedSystemPrompt.value = systemPrompts.value.find(p => p.name === 'default')?.id;
  }
};

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
  loadSystemPrompts();
});
</script>

<template>
  <Head title="Chat" />

  <AuthenticatedLayout>
    <div v-if="! props.prioritizeHistory" class="flex justify-end items-end">
      <div class="flex justify-center w-3/4">
        <h1 class="pt-2 text-lg">{{ chatTitle }}</h1>    
      </div>
      <div class="flex flex-col w-1/2 md:w-1/4 lg:w-1/4">
        <button @click="toggleSystemPromptDropDown" class="text-gray-700 hover:text-blue-500 px-4 py-2 rounded-lg text-sm">
          set system prompt
        </button>
        <div v-if="systemPromptDropDown" class="mt-2 w-48 bg-white border border-gray-300 rounded-lg shadow-lg">
          <select v-model="selectedSystemPrompt" @change="applyPrompt" class="w-full p-2 border border-gray-300 rounded-lg">
            <option v-for="prompt in systemPrompts" :key="prompt.id" :value="prompt.id">{{ prompt.name }}</option>
          </select>
        </div>
      </div>
    </div>

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
              <div v-if="sendLoading" class="animate-spin rounded-full h-8 w-8 border-t-4 border-b-4 border-purple-500"></div>
              <button v-else @click="sendMessage" class="bg-blue-500 text-white px-4 py-2 rounded-lg disabled:bg-gray-300"
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
