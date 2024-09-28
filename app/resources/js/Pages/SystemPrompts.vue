<script setup>
import { Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { ref } from 'vue';
import NewPromptModal from '@/Components/SystemPrompts/NewPromptModal.vue';
import axios from 'axios';

const props = defineProps({
  prompts: {
    type: Array,
    required: true
  }
});

const prompts = ref(props.prompts);
const showModal = ref(false);
const error = ref(false);
const editingPrompt = ref(null);

const closeModal = () => {
  showModal.value = false;
  editingPrompt.value = null;
};

const deletePrompt = async (promptId) => {
  try {
    await axios.delete(`/system-prompts/${promptId}`);
    prompts.value = prompts.value.filter( p => p.id !== promptId);
  } catch (err) {
    console.error({ err });

    error.value = err.response.data.message;   

    setTimeout(() => {
      error.value = false;
    }, 5000);
  }

  showModal.value = false;
  editingPrompt.value = null;
};

const savePrompt = async (newPrompt) => {
  try {
    const response = await axios.post('/system-prompts', newPrompt);
    const savedPrompt = response.data;
    
    const matchIndex = prompts.value.findIndex( p => p.id === savedPrompt.id);

    if (matchIndex > 0) {
      prompts.value[matchIndex] = savedPrompt;
    } else {
      prompts.value.unshift(savedPrompt);
    }
  } catch (err) {
    console.error({ err });

    error.value = err.response.data.message;   

    setTimeout(() => {
      error.value = false;
    }, 5000);
  }

  showModal.value = false;
  editingPrompt.value = null;
};

const editPrompt = (prompt) => {
  editingPrompt.value = prompt;
  showModal.value = true;
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
        <div class="flex items-center justify-between">
          <h2 class="text-xl font-semibold">{{ prompt.name }}</h2>
          <button v-if="prompt.user_id !== null" @click="editPrompt(prompt)" class="text-black hover:text-blue-500 px-4 py-2 rounded-lg mt-2">Edit</button>
        </div>
        <p class="text-gray-700">{{ prompt.prompt }}</p>
      </div>
    </div>
    
    <NewPromptModal v-if="showModal" :prompt="editingPrompt" @close="closeModal" @save="savePrompt" @delete="deletePrompt"/>
  </AuthenticatedLayout>
</template>

<style scoped>
.container {
  max-width: 800px;
}
</style>