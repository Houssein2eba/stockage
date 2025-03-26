<script setup>

const model=defineModel(
    {
        type: String,
        required: true,
    }
);
const props=defineProps({
    
    options: {
        type: Array,
        required: true,
    },
    
});

</script>
<template>
    
        
        <select
            v-model="model"
            @input="$emit('update:model', $event.target.value)"
            class="mt-1 block w-full rounded-md border-gray-300 bg-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
            
            <option v-for="option in options" :value="option.name" :key="option.name">{{ option.name }}</option>
        </select>
   
</template>
