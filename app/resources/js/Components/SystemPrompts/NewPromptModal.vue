<template>
  <div class="fixed inset-0 flex items-center justify-center bg-gray-800 bg-opacity-75 p-4">
    <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-lg">
      <h2 class="text-xl font-bold mb-4">Add New Prompt</h2>
      <form @submit.prevent="savePrompt">
        <div class="mb-2">
          <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
          <input v-model="newPrompt.name" type="text" id="name" class="mt-1 p-2 border border-gray-300 rounded-md w-full" required />
        </div>
        <div class="mb-2">
          <label for="prompt" class="block text-sm font-medium text-gray-700">Prompt</label>
          <textarea v-model="newPrompt.prompt" id="prompt" class="mt-1 p-2 border border-gray-300 rounded-md w-full" rows="10" required></textarea>
        </div>
        <div class="flex justify-end space-x-2">
          <button v-if="props.prompt?.id" type="button" @click="deletePrompt" class="bg-red-500 text-white px-4 py-2 rounded-lg">Delete</button>
          <button type="button" @click="emit('close')" class="bg-gray-500 text-white px-4 py-2 rounded-lg">Cancel</button>
          <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-lg">Save</button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, defineEmits, defineProps } from 'vue';
const props = defineProps({
  prompt: Object,
  default: null,
  required: false,
});

const newPrompt = ref({ 
  name: props.prompt?.name ?? '', 
  prompt: props.prompt?.prompt ?? '', 
});
const emit = defineEmits(['close', 'save', 'delete']);

const deletePrompt = () => {
  const confirmation = confirm('Are you sure you want to delete this prompt?');
  if (confirmation) {
    emit('delete', props.prompt?.id);
  } else {
    emit('close');
  }
};

const savePrompt = () => {
  if (
    newPrompt.value.name === props.prompt?.name && 
    newPrompt.value.prompt === props.prompt?.prompt
  ) {
    emit('close');
    return;
  }

  emit('save', { id: props.prompt?.id, ...newPrompt.value });

  newPrompt.value.name = '';
  newPrompt.value.prompt = '';

  emit('close');
};
</script>