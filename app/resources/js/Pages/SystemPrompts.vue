<script setup>
import { Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { toRefs } from 'vue';

const props = defineProps({
  prompts: {
    type: Array,
    required: true
  }
});

const { prompts } = toRefs(props);
</script>
<template>
  <Head title="System Prompts" />
  <AuthenticatedLayout>
    <div class="container mx-auto p-4">
      <h1 class="text-2xl font-bold mb-4">System Prompts</h1>
      <div 
        v-for="prompt in prompts" 
        :key="prompt.id" 
        :class="['shadow-md rounded-lg p-4 mb-4', prompt.user_id === null ? 'bg-gray-100' : 'bg-white']"
      >
        <h2 class="text-xl font-semibold">{{ prompt.name }}</h2>
        <p class="text-gray-700">{{ prompt.prompt }}</p>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<style scoped>
.container {
  max-width: 800px;
}
</style>