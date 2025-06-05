<template>
  <th scope="col" :colspan="colspan" class="px-4 py-3 bg-gray-50 hover:bg-gray-200 text-left text-xs font-medium text-black uppercase tracking-wider whitespace-nowrap">
    <slot />
  </th>
</template>

<script setup>
import { defineProps } from "vue";
defineProps({
  colspan: {
    type: Number,
    default: 1
  }
});
</script>
