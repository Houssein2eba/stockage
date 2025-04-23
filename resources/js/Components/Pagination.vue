<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    links: Array,
});

const emit = defineEmits(['change']);

const handleLinkClick = (url) => {
    emit('change', url);
};
</script>

<template>
    <div v-if="links.length > 3" class="flex items-center justify-between mt-6">
        <div class="flex flex-wrap -mb-1">
            <template v-for="(link, key) in links" :key="key">
                <div v-if="link.url === null" 
                     class="mr-1 mb-1 px-4 py-3 text-sm leading-4 text-gray-400 border rounded" 
                     v-html="link.label" />
                <Link v-else
                      :href="link.url"
                      class="mr-1 mb-1 px-4 py-3 text-sm leading-4 border rounded hover:bg-gray-100 focus:border-indigo-500 focus:text-indigo-500"
                      :class="{ 'bg-indigo-500 text-white border-indigo-500': link.active }"
                      v-html="link.label"
                      @click.prevent="handleLinkClick(link.url)" />
            </template>
        </div>
    </div>
</template>