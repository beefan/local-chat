<script setup>
import { router } from '@inertiajs/vue3';
import { inject, computed } from 'vue';

const props = defineProps({
  chats: {
    type: Array,
    required: false,
  },
  current: {
    type: Number,
    required: false,
    default: null,
  },
});

const formatDate = (dateString) => {
  const options = { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' };
  return new Date(dateString).toLocaleDateString(undefined, options);
};

const goToChat = (chat) => {
  router.get(`/chat/${chat}`, {}, { only: ['id', 'title', 'messageHistory', 'prioritizeHistory'] });
};
const newChat = () => {
  router.get('/chat');
};

const screenIsSmall = inject('screenIsSmall');
const prioritizeHistory = inject('prioritizeHistory');

const wrapperClass = computed(() => {
  return `bg-gray-200 rounded-lg mx-4 pb-6 ${screenIsSmall ? 'w-full' : 'w-1/4'}`;
});
const chatClass = (chatId) => {
  return 'border-b first:border-t border-gray-300 p-4 hover:bg-purple-200 hover:cursor-pointer' + (props.current === chatId ? ' bg-purple-200' : '');
};
</script>
<template>
  <div :class="wrapperClass" v-if="props.chats && !(screenIsSmall && !prioritizeHistory)">
    <div class="flex justify-between items-center px-4 pt-4 mb-4">
      <h2 class="text-xl font-bold">Chat History</h2>
      <button @click="newChat" class="px-4 py-2 rounded-lg font-bold border border-gray-500 hover:bg-purple-500 hover:border-purple-500">New</button>
    </div>

    <div v-for="(chat, index) in chats" :key="index" @click="goToChat(chat.id)" :class="chatClass(chat.id)">
      <div class="text-sm text-gray-500">{{ formatDate(chat.created_at) }}</div>
      <div class="text-sm font-bold">{{ chat.title }}</div>
      <div v-if="chat.summary" class="text-sm">{{ chat.summary }}</div>
    </div>
  </div>
</template>