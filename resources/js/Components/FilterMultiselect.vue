<script setup>
import Multiselect from 'vue-multiselect';

defineProps({
    modelValue: {
        type: [Array, Object, String, Number],
        default: null
    },
    options: {
        type: Array,
        required: true
    },
    label: {
        type: String,
        default: ''
    },
    trackBy: {
        type: String,
        default: 'id'
    },
    valueProperty: {
        type: String,
        default: 'id'
    },
    labelProperty: {
        type: String,
        default: 'name'
    },
    placeholder: {
        type: String,
        default: 'Select option'
    },
    multiple: {
        type: Boolean,
        default: false
    },
    searchable: {
        type: Boolean,
        default: true
    },
    clearOnSelect: {
        type: Boolean,
        default: false
    },
    closeOnSelect: {
        type: Boolean,
        default: true
    },
    disabled: {
        type: Boolean,
        default: false
    },
    loading: {
        type: Boolean,
        default: false
    },
    preselectFirst: {
        type: Boolean,
        default: false
    },
    preserveSearch: {
        type: Boolean,
        default: false
    },
    allowEmpty: {
        type: Boolean,
        default: true
    },
    resetAfter: {
        type: Boolean,
        default: false
    },
    maxHeight: {
        type: Number,
        default: 300
    },
    showLabels: {
        type: Boolean,
        default: true
    }
});

const emit = defineEmits(['update:modelValue', 'select', 'remove', 'search-change', 'open', 'close']);

const updateValue = (value) => {
    emit('update:modelValue', value);
};
</script>

<template>
    <div class="filter-multiselect">
        <label v-if="label" class="block text-sm font-medium text-gray-700 mb-1">
            {{ label }}
        </label>
        <Multiselect
            v-model="modelValue"
            :options="options"
            :multiple="multiple"
            :track-by="trackBy"
            :label="labelProperty"
            :searchable="searchable"
            :close-on-select="closeOnSelect"
            :clear-on-select="clearOnSelect"
            :placeholder="placeholder"
            :allow-empty="allowEmpty"
            :reset-after="resetAfter"
            :preserve-search="preserveSearch"
            :preselect-first="preselectFirst"
            :max-height="maxHeight"
            :show-labels="showLabels"
            :disabled="disabled"
            :loading="loading"
            @update:modelValue="updateValue"
            @select="$emit('select', $event)"
            @remove="$emit('remove', $event)"
            @search-change="$emit('search-change', $event)"
            @open="$emit('open')"
            @close="$emit('close')"
            class="border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm"
        >
            <template #noResult>
                <span class="text-gray-500 text-sm py-1 px-2">No results found</span>
            </template>
            <template #noOptions>
                <span class="text-gray-500 text-sm py-1 px-2">No options available</span>
            </template>
            <template v-if="$slots.tag" #tag="props">
                <slot name="tag" v-bind="props"></slot>
            </template>
            <template v-if="$slots.option" #option="props">
                <slot name="option" v-bind="props"></slot>
            </template>
            <template v-if="$slots.maxElements" #maxElements="props">
                <slot name="maxElements" v-bind="props"></slot>
            </template>
            <template v-if="$slots.beforeList" #beforeList>
                <slot name="beforeList"></slot>
            </template>
            <template v-if="$slots.afterList" #afterList>
                <slot name="afterList"></slot>
            </template>
        </Multiselect>
    </div>
</template>

<style>
/* Add any custom styles to extend vue-multiselect base styles */
.filter-multiselect .multiselect {
    min-height: 38px;
}

.filter-multiselect .multiselect__tags {
    min-height: 38px;
    padding: 5px 40px 0 8px;
    border-radius: 0.375rem;
    border-color: #d1d5db; /* gray-300 */
}

.filter-multiselect .multiselect__placeholder {
    margin-bottom: 8px;
    padding-top: 0;
    color: #6b7280; /* gray-500 */
}

.filter-multiselect .multiselect__input,
.filter-multiselect .multiselect__single {
    margin-bottom: 5px;
    padding-left: 0;
    background: transparent;
}

.filter-multiselect .multiselect--active {
    z-index: 30; /* Ensure dropdown appears above other elements */
}

.filter-multiselect .multiselect__select {
    height: 38px;
}

.filter-multiselect .multiselect__option--highlight {
    background: #3b82f6; /* blue-500 */
}

.filter-multiselect .multiselect__option--selected.multiselect__option--highlight {
    background: #ef4444; /* red-500 */
}

.filter-multiselect .multiselect__tag {
    background: #3b82f6; /* blue-500 */
    margin-bottom: 3px;
}

.filter-multiselect .multiselect__tag-icon:after {
    color: #ffffff;
}

.filter-multiselect .multiselect__tag-icon:hover {
    background: #2563eb; /* blue-600 */
}

.filter-multiselect .multiselect--disabled {
    background: #f3f4f6; /* gray-100 */
    opacity: 0.7;
}
</style> 