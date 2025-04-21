<template>
  <th scope="col" :colspan="colspan" class="px-4 py-3 bg-slate-700 text-left text-xs font-medium text-white uppercase tracking-wider whitespace-nowrap">
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