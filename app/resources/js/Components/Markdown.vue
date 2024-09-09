<script setup>
import markdownit from 'markdown-it';
import { ref } from 'vue';

const markdown = markdownit({
  html: true,
  linkify: true,
  typographer: true,
});

const props = defineProps({
  content: {
    type: String,
  }
});
const blocks = ref(
  props.
    content.
    split('\n').
    filter((i) => i.length).
    map((i) => markdown.render(i))
);

</script>
<template>
  <div v-for="block, i in blocks">
    <div v-html="block"></div>
    <br v-if="i < blocks.length - 1" />
  </div>
</template>