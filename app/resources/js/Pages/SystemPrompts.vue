<script setup>
import { Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { toRefs, ref } from 'vue';
import NewPromptModal from '@/Components/SystemPrompts/NewPromptModal.vue';
import axios from 'axios';

const props = defineProps({
  prompts: {
    type: Array,
    required: true
  }
});

const { prompts } = toRefs(props);
const showModal = ref(false);
const error = ref(false);

const addPrompt = async (newPrompt) => {
  try {
    const response = await axios.post('/system-prompts', newPrompt);
    const savedPrompt = response.data;
    
    prompts.value.unshift(savedPrompt);

    showModal.value = false;
  } catch (err) {
    error.value = err.response.data.message;

    setTimeout(() => {
      error.value = false;
    }, 5000);
  }
};
</script>

<template>
  <Head title="System Prompts" />
  <AuthenticatedLayout>
    <div class="container mx-auto p-4">
      <div class="flex flex-col justify-between mb-4 lg:flex-row md:flex-row">
        <h1 class="text-2xl font-bold">System Prompts</h1>
        <div v-if="error" class="bg-red-100 border border-red-400 text-red-700 px-4 py-2 m-2 rounded-lg">
          {{ error }} Please try again.
        </div>
        <button @click="showModal = true" class="bg-blue-500 text-white px-4 py-2 sm:mt-2 rounded-lg max-w-48">Add New Prompt</button>
      </div>      
      <div 
        v-for="prompt in prompts" 
        :key="prompt.id" 
        :class="['shadow-md rounded-lg p-4 mb-4', prompt.user_id === null ? 'bg-gray-100' : 'bg-white']"
      >
        <h2 class="text-xl font-semibold">{{ prompt.name }}</h2>
        <p class="text-gray-700">{{ prompt.prompt }}</p>
      </div>
    </div>
    
    <NewPromptModal v-if="showModal" @close="showModal = false" @save="addPrompt" />
  </AuthenticatedLayout>
</template>

<style scoped>
.container {
  max-width: 800px;
}
</style>