<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    meta: {
        type: Object,
        required: true
    },
    showInfo: {
        type: Boolean,
        default: true
    },
    size: {
        type: String,
        default: 'md', // sm, md, lg
        validator: (value) => ['sm', 'md', 'lg'].includes(value)
    },
    align: {
        type: String,
        default: 'center', // start, center, end
        validator: (value) => ['start', 'center', 'end'].includes(value)
    }
});

// Dynamic classes based on size
const buttonClasses = computed(() => {
    const sizes = {
        sm: 'px-2 py-1 text-xs',
        md: 'px-3.5 py-2 text-sm',
        lg: 'px-4 py-2.5 text-base'
    };
    return sizes[props.size] || sizes.md;
});

// Alignment classes
const alignmentClasses = computed(() => {
    const alignments = {
        start: 'justify-start',
        center: 'justify-center',
        end: 'justify-end'
    };
    return alignments[props.align] || alignments.center;
});

// Calculate the current page range being displayed
const pageRange = computed(() => {
    if (!props.meta) return { from: 0, to: 0 };
    
    // Handle different data structures
    const current_page = props.meta.current_page || props.meta.current || 1;
    const per_page = props.meta.per_page || 10;
    const total = props.meta.total || 0;
    
    const from = ((current_page - 1) * per_page) + 1;
    const to = Math.min(current_page * per_page, total);
    return { from, to };
});

// Generate array of page numbers to display
const paginationLinks = computed(() => {
    if (!props.meta) return [];
    
    // Handle different data structures
    const last_page = props.meta.last_page || props.meta.last || 1;
    const current_page = props.meta.current_page || props.meta.current || 1;
    
    // Define how many page numbers to show (excluding prev and next)
    const maxVisiblePages = 5;
    
    let startPage = Math.max(1, current_page - Math.floor(maxVisiblePages / 2));
    let endPage = Math.min(last_page, startPage + maxVisiblePages - 1);
    
    // Adjust if we're near the end
    if (endPage - startPage + 1 < maxVisiblePages) {
        startPage = Math.max(1, endPage - maxVisiblePages + 1);
    }
    
    const pages = [];
    
    // Previous button
    pages.push({
        label: 'Previous',
        url: current_page > 1 ? getPageUrl(current_page - 1) : null,
        icon: true,
        active: false
    });
    
    // First page with ellipsis if needed
    if (startPage > 1) {
        pages.push({
            label: '1',
            url: getPageUrl(1),
            active: current_page === 1
        });
        
        if (startPage > 2) {
            pages.push({
                label: '...',
                url: null,
                ellipsis: true
            });
        }
    }
    
    // Page numbers
    for (let i = startPage; i <= endPage; i++) {
        pages.push({
            label: i.toString(),
            url: getPageUrl(i),
            active: current_page === i
        });
    }
    
    // Last page with ellipsis if needed
    if (endPage < last_page) {
        if (endPage < last_page - 1) {
            pages.push({
                label: '...',
                url: null,
                ellipsis: true
            });
        }
        
        pages.push({
            label: last_page.toString(),
            url: getPageUrl(last_page),
            active: current_page === last_page
        });
    }
    
    // Next button
    pages.push({
        label: 'Next',
        url: current_page < last_page ? getPageUrl(current_page + 1) : null,
        icon: true,
        active: false
    });
    
    return pages;
});

// Generate URL for a specific page
function getPageUrl(page) {
    // Use current URL and replace page parameter
    const url = new URL(window.location.href);
    url.searchParams.set('page', page.toString());
    return url.pathname + url.search;
}
</script>

<template>
    <div v-if="meta && (meta.last_page > 1 || meta.last > 1)" class="mt-6 w-full">
        <!-- Page Information -->
        <div v-if="showInfo" class="text-sm text-gray-600 mb-3 text-center">
            Showing {{ pageRange.from }} to {{ pageRange.to }} of {{ meta.total }} results
        </div>

        <!-- Pagination Controls -->
        <div :class="['flex flex-wrap gap-1', alignmentClasses]">
            <template v-for="(link, key) in paginationLinks" :key="key">
                <!-- Previous Button -->
                <template v-if="link.label === 'Previous'">
                    <div v-if="link.url === null"
                         :class="[
                             buttonClasses,
                             'flex items-center gap-1 rounded-md border border-gray-200 bg-gray-50 text-gray-400 opacity-50 cursor-not-allowed'
                         ]">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                        <span class="sr-only">Previous</span>
                    </div>
                    <Link v-else
                          :href="link.url"
                          :class="[
                              buttonClasses,
                              'flex items-center gap-1 rounded-md border border-gray-200 bg-white text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors duration-200'
                          ]">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                        <span class="sr-only">Previous</span>
                    </Link>
                </template>

                <!-- Next Button -->
                <template v-else-if="link.label === 'Next'">
                    <div v-if="link.url === null"
                         :class="[
                             buttonClasses,
                             'flex items-center gap-1 rounded-md border border-gray-200 bg-gray-50 text-gray-400 opacity-50 cursor-not-allowed'
                         ]">
                        <span class="sr-only">Next</span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <Link v-else
                          :href="link.url"
                          :class="[
                              buttonClasses,
                              'flex items-center gap-1 rounded-md border border-gray-200 bg-white text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors duration-200'
                          ]">
                        <span class="sr-only">Next</span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                        </svg>
                    </Link>
                </template>

                <!-- Ellipsis -->
                <div v-else-if="link.ellipsis"
                     :class="[
                         buttonClasses,
                         'flex items-center justify-center rounded-md border border-gray-200 bg-white text-gray-500'
                     ]">
                    &hellip;
                </div>

                <!-- Regular Page Number -->
                <template v-else>
                    <Link :href="link.url"
                          :class="[
                              buttonClasses,
                              'flex items-center justify-center rounded-md transition-all duration-200 min-w-[2.5rem]',
                              link.active 
                                ? 'border border-blue-600 bg-gradient-to-r from-blue-500 to-indigo-500 text-white font-medium shadow-sm hover:from-blue-600 hover:to-indigo-600' 
                                : 'border border-gray-200 bg-white text-gray-700 hover:bg-blue-50 hover:text-blue-600'
                          ]">
                        {{ link.label }}
                    </Link>
                </template>
            </template>
        </div>
    </div>
</template>
