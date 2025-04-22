<script setup>
import { computed } from "vue";
import { Link } from "@inertiajs/vue3";

const props = defineProps(["href", "active"]);

const classes = computed(() =>
  props.active
    ? "relative px-4 py-3 flex items-center space-x-4 rounded-lg text-blue-600 bg-blue-50/80 font-medium transition-all duration-200 transform hover:scale-[1.02] hover:shadow-md"
    : "px-4 py-3 flex items-center space-x-4 rounded-lg text-gray-600 hover:text-blue-600 hover:bg-blue-50/60 transition-all duration-200 hover:shadow-sm"
);
</script>

<template>
  <Link :href="href" :class="classes">
    <slot />
  </Link>
</template>
