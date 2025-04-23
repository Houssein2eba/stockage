<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    links: Array,
});
</script>

<template>
    <div v-if="links && links.length > 3" class="flex items-center justify-between mt-6">
        <div class="flex flex-wrap -mb-1 mx-auto">
            <template v-for="(link, key) in links" :key="key">
                <div v-if="link.url === null" 
                     class="mr-1.5 mb-1 px-4 py-2 text-sm leading-4 text-gray-400 border border-gray-200 rounded-md opacity-60" 
                     v-html="link.label" />
                <Link v-else
                      :href="link.url"
                      class="mr-1.5 mb-1 px-4 py-2 text-sm leading-4 border border-gray-200 rounded-md transition-all duration-200 shadow-sm hover:bg-blue-50 hover:border-blue-200 focus:ring-2 focus:ring-blue-200 focus:outline-none"
                      :class="{ 'bg-gradient-to-r from-blue-500 to-indigo-600 text-white border-blue-500 hover:bg-blue-600 hover:from-blue-600 hover:to-indigo-700 shadow-md': link.active }"
                      v-html="link.label" />
            </template>
        </div>
    </div>
</template>