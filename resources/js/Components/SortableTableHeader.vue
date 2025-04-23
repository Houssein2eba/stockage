<script setup>
import TableHeaderCell from '@/Components/TableHeaderCell.vue';

const props = defineProps({
    label: {
        type: String,
        required: true
    },
    field: {
        type: String,
        default: null
    },
    sortable: {
        type: Boolean,
        default: false
    },
    sortDirection: {
        type: String,
        default: 'none',
        validator: (value) => ['asc', 'desc', 'none'].includes(value)
    },
    colspan: {
        type: Number,
        default: 1
    },
    align: {
        type: String,
        default: 'left',
        validator: (value) => ['left', 'center', 'right'].includes(value)
    }
});

const emit = defineEmits(['sort']);

const handleClick = () => {
    if (props.sortable && props.field) {
        emit('sort', props.field);
    }
};

const textAlignClass = {
    'left': 'text-left',
    'center': 'text-center',
    'right': 'text-right'
};
</script>

<template>
    <TableHeaderCell 
        :class="[
            sortable ? 'cursor-pointer hover:bg-gray-100' : '',
            textAlignClass[align],
            'px-4 sm:px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap transition-colors'
        ]"
        :colspan="colspan"
        @click="handleClick"
    >
        <div class="flex items-center" :class="{ 'justify-center': align === 'center', 'justify-end': align === 'right' }">
            <span>{{ label }}</span>
            <span v-if="sortable" class="flex flex-col ml-1 h-3">
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-3 w-3 -mb-1"
                    :class="{'text-blue-600': sortDirection === 'asc'}"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                >
                    <path fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd" />
                </svg>
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-3 w-3 -mt-1"
                    :class="{'text-blue-600': sortDirection === 'desc'}"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                >
                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
            </span>
        </div>
    </TableHeaderCell>
</template> 