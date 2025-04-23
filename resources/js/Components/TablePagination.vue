<script setup>
import { Link } from '@inertiajs/vue3';
import Pagination from '@/Components/Pagination.vue';

const props = defineProps({
    meta: Object,
    links: Array,
    showSummary: {
        type: Boolean,
        default: true
    },
    alignment: {
        type: String,
        default: 'between',  // 'start', 'center', 'end', 'between'
        validator: (value) => ['start', 'center', 'end', 'between'].includes(value)
    }
});

const emit = defineEmits(['change']);

const handleLinkClick = (url) => {
    emit('change', url);
};

const justifyClass = {
    'start': 'justify-start',
    'center': 'justify-center',
    'end': 'justify-end',
    'between': 'justify-between'
};
</script>

<template>
    <div v-if="meta && meta.links && meta.links.length > 3" class="px-6 py-4 border-t border-gray-200">
        <div class="flex items-center flex-wrap" :class="justifyClass[alignment]">
            <!-- Results summary -->
            <div v-if="showSummary && meta.total > 0" class="text-sm text-gray-700 mb-2 sm:mb-0">
                Showing <span class="font-medium">{{ meta.from }}</span> to 
                <span class="font-medium">{{ meta.to }}</span> of 
                <span class="font-medium">{{ meta.total }}</span> results
            </div>
            
            <!-- Pagination links -->
            <Pagination :links="meta.links || links" @change="handleLinkClick" />
        </div>
    </div>
</template> 