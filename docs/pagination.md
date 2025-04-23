# Pagination System Documentation

This document outlines our standardized pagination system for use across the application. The system consists of several main components:

1. `usePagination` composable for state management and business logic
2. `TablePagination` component for UI display
3. `SortableTableHeader` component for table sorting
4. `FilterMultiselect` component for advanced filtering using vue-multiselect

## Getting Started

### Basic Implementation

To implement pagination in a new page or component:

```vue
<script setup>
import { ref, watch } from 'vue';
import { usePagination } from '@/composables/usePagination';
import TablePagination from '@/Components/TablePagination.vue';
import SortableTableHeader from '@/Components/SortableTableHeader.vue';

const props = defineProps({
    // Your paginated data from backend
    items: Object,
    filters: Object
});

// Setup pagination with the composable
const {
    search,
    sort,
    page,
    handleSort,
    handlePageChange,
    getSortIcon,
    updateRoute
} = usePagination({
    routeName: 'your.route.name',
    initialFilters: props.filters,
    initialMeta: props.items?.meta,
    watchDependencies: [search],
    debounceTime: 300
});
</script>

<template>
    <div>
        <!-- Search input connected to the search ref -->
        <input v-model="search" type="search" placeholder="Search..." />
        
        <!-- Table with sortable headers -->
        <table>
            <thead>
                <tr>
                    <SortableTableHeader 
                        label="Name" 
                        field="name" 
                        :sortable="true"
                        :sortDirection="getSortIcon('name')"
                        @sort="handleSort"
                    />
                    <SortableTableHeader 
                        label="Date" 
                        field="created_at" 
                        :sortable="true"
                        :sortDirection="getSortIcon('created_at')"
                        @sort="handleSort"
                    />
                </tr>
            </thead>
            <!-- Table body -->
        </table>
        
        <!-- Pagination component -->
        <TablePagination 
            :meta="items.meta" 
            @change="handlePageChange" 
        />
    </div>
</template>
```

## Components Reference

### 1. usePagination Composable

This composable manages pagination state and logic.

#### Parameters

```js
usePagination({
    routeName,            // Route name for the page (required)
    initialFilters = {},  // Initial filter values from props
    initialMeta = {},     // Initial pagination metadata
    watchDependencies = [], // Reactive variables to watch for changes
    additionalParams = {},  // Additional parameters to include in requests
    debounceTime = 300    // Debounce time for search in milliseconds
})
```

#### Returns

```js
{
    // State
    page,       // Current page number (ref)
    search,     // Search query (ref)
    sort,       // Sort configuration { field, direction } (ref)
    filters,    // Additional filters object (ref)
    
    // Methods
    handlePageChange, // Function to handle pagination link clicks
    handleSort,       // Function to handle sorting column clicks
    getSortIcon,      // Function to get sort icon for a column
    updateRoute,      // Function to update the route with current filters
    setFilters        // Function to set multiple filter values at once
}
```

### 2. TablePagination Component

This component displays the pagination UI with results summary.

#### Props

```js
{
    meta: Object,          // Pagination metadata from backend
    links: Array,          // Pagination links (if not using meta)
    showSummary: Boolean,  // Whether to show results summary (default: true)
    alignment: String      // Alignment of pagination elements: 'start', 'center', 'end', 'between' (default: 'between')
}
```

#### Events

```js
{
    change: (url) => void  // Emitted when a pagination link is clicked
}
```

### 3. SortableTableHeader Component

This component displays a sortable table header.

#### Props

```js
{
    label: String,         // Header label (required)
    field: String,         // Field name for sorting (null for non-sortable columns)
    sortable: Boolean,     // Whether the column is sortable (default: false)
    sortDirection: String, // Current sort direction: 'asc', 'desc', 'none' (default: 'none')
    colspan: Number,       // Column span (default: 1)
    align: String          // Text alignment: 'left', 'center', 'right' (default: 'left')
}
```

#### Events

```js
{
    sort: (field) => void  // Emitted when the header is clicked for sorting
}
```

### 4. FilterMultiselect Component

This component provides an advanced dropdown selection using vue-multiselect.

#### Props

```js
{
    modelValue: [Array, Object, String, Number], // v-model value
    options: Array,                   // Array of options (required)
    label: String,                    // Label for the input field
    trackBy: String,                  // Property to track in options objects (default: 'id')
    valueProperty: String,            // Property to use as value (default: 'id')
    labelProperty: String,            // Property to display (default: 'name')
    placeholder: String,              // Placeholder text
    multiple: Boolean,                // Allow multiple selections
    searchable: Boolean,              // Allow searching in dropdown (default: true)
    clearOnSelect: Boolean,           // Clear search input on select
    closeOnSelect: Boolean,           // Close dropdown on select (default: true)
    disabled: Boolean,                // Disable the select
    loading: Boolean,                 // Show loading state
    // ... and more vue-multiselect props
}
```

#### Events

```js
{
    'update:modelValue': (value) => void,  // Value change
    'select': (option) => void,            // Option selected
    'remove': (option) => void,            // Option removed 
    'search-change': (searchQuery) => void // Search query changed
    // ... and more vue-multiselect events
}
```

## Backend Requirements

For this pagination system to work properly, the backend should return paginated data in the following format:

```json
{
    "data": [
        // Array of items
    ],
    "meta": {
        "current_page": 1,
        "from": 1,
        "to": 10,
        "total": 50,
        "per_page": 10,
        "last_page": 5,
        "links": [
            {
                "url": null,
                "label": "&laquo; Previous",
                "active": false
            },
            {
                "url": "https://example.com/api/items?page=1",
                "label": "1",
                "active": true
            },
            // More pagination links...
            {
                "url": "https://example.com/api/items?page=2",
                "label": "Next &raquo;",
                "active": false
            }
        ]
    }
}
```

## Advanced Usage

### With Additional Filters

```js
// Additional filter state
const filters = ref({
    status: props.filters?.status || '',
    category: props.filters?.category || null
});

const {
    search,
    sort,
    page,
    handleSort,
    handlePageChange,
    getSortIcon,
    updateRoute,
    setFilters
} = usePagination({
    routeName: 'items.index',
    initialFilters: props.filters,
    initialMeta: props.items?.meta,
    watchDependencies: [search],
    additionalParams: { 
        status: filters.value.status,
        category: filters.value.category
    }
});

// Watch for filter changes
watch(filters, (newFilters) => {
    updateRoute({
        status: newFilters.status,
        category: newFilters.category,
        page: 1 // Reset to page 1 when filtering
    });
}, { deep: true });
```

### Using FilterMultiselect with Pagination

The FilterMultiselect component works seamlessly with the pagination system:

```vue
<script setup>
import { ref } from 'vue';
import { usePagination } from '@/composables/usePagination';
import FilterMultiselect from '@/Components/FilterMultiselect.vue';

const props = defineProps({
    items: Object,
    filters: Object,
    categories: Array
});

// Status options for multiselect
const statusOptions = [
    { label: 'All Status', value: '' },
    { label: 'Active', value: 'active' },
    { label: 'Inactive', value: 'inactive' }
];

// Initialize with values from filters
const statusFilter = ref(
    props.filters?.status 
        ? statusOptions.find(option => option.value === props.filters.status) 
        : statusOptions[0]
);

// Initialize pagination
const {
    search,
    setFilters
} = usePagination({
    routeName: 'items.index',
    initialFilters: props.filters,
    initialMeta: props.items?.meta,
    watchDependencies: [search]
});

// Handle status filter change
const handleStatusChange = (value) => {
    setFilters({ status: value?.value || '' });
};

// Handle category filter change (multiple selection)
const handleCategoryChange = (value) => {
    // The composable will automatically handle array values
    setFilters({ categories: value });
};
</script>

<template>
    <div class="filters">
        <!-- Search input -->
        <input v-model="search" type="search" placeholder="Search..." />
        
        <!-- Status filter (single select) -->
        <FilterMultiselect
            v-model="statusFilter"
            :options="statusOptions"
            label="Status"
            placeholder="Select status"
            track-by="value"
            @update:modelValue="handleStatusChange"
        />
        
        <!-- Category filter (multi select) -->
        <FilterMultiselect
            v-model="selectedCategories"
            :options="props.categories"
            label="Categories"
            placeholder="Select categories"
            :multiple="true"
            @update:modelValue="handleCategoryChange"
        />
    </div>
</template>
```

### Custom Sorting Fields

You can provide a list of sortable fields to the handleSort method:

```js
const sortableFields = ['name', 'created_at', 'total'];

// In template
<SortableTableHeader 
    label="Name" 
    field="name" 
    :sortable="true"
    :sortDirection="getSortIcon('name')"
    @sort="(field) => handleSort(field, sortableFields)"
/>
``` 