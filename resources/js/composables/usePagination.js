import { ref, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import { debounce } from 'lodash';

/**
 * Composable for handling pagination functionality
 * 
 * @param {Object} options - Configuration options
 * @param {string} options.routeName - The route name for pagination
 * @param {Object} options.initialFilters - Initial filter values from props
 * @param {Object} options.initialMeta - Initial pagination metadata
 * @param {Array} options.watchDependencies - Reactive variables to watch for changes
 * @param {Object} options.additionalParams - Additional parameters to include in requests
 * @param {number} options.debounceTime - Debounce time for search in milliseconds
 * @returns {Object} Pagination utilities and state
 */
export function usePagination(options) {
    const {
        routeName,
        initialFilters = {},
        initialMeta = {},
        watchDependencies = [],
        additionalParams = {},
        debounceTime = 300
    } = options;

    // Pagination state
    const page = ref(initialMeta?.current_page || 1);
    const search = ref(initialFilters?.search || '');
    const sort = ref({
        field: initialFilters?.sort || 'created_at',
        direction: initialFilters?.direction || 'desc'
    });

    // Watch for filter changes to reset pagination to page 1
    if (watchDependencies.length > 0) {
        watch(watchDependencies, debounce(() => {
            updateRoute({ page: 1 });
        }, debounceTime));
    }

    /**
     * Process filter values for the request
     * Handles multiselect values properly (arrays/objects)
     * 
     * @param {Object} params - The parameters to process
     * @returns {Object} - The processed parameters
     */
    const processParams = (params) => {
        const processed = { ...params };
        
        // Process multiselect values - convert objects/arrays to appropriate format
        Object.keys(processed).forEach(key => {
            const value = processed[key];
            
            // Handle array values (multiple selections)
            if (Array.isArray(value)) {
                if (value.length === 0) {
                    processed[key] = null;
                } else if (typeof value[0] === 'object' && value[0] !== null) {
                    // Array of objects - extract ids or specified property
                    processed[key] = value.map(item => item.id || item.value || item).join(',');
                } else {
                    // Array of primitives
                    processed[key] = value.join(',');
                }
            } 
            // Handle object values (single selection)
            else if (value && typeof value === 'object' && !Array.isArray(value)) {
                processed[key] = value.id || value.value || null;
            }
        });
        
        return processed;
    };

    /**
     * Helper function to update the route with current filters
     * @param {Object} overrides - Values to override in the request
     */
    const updateRoute = (overrides = {}) => {
        const params = {
            search: search.value,
            sort: sort.value.field,
            direction: sort.value.direction,
            page: page.value,
            ...additionalParams,
            ...overrides
        };

        router.get(route(routeName), processParams(params), {
            preserveState: true,
            preserveScroll: true
        });
    };

    /**
     * Handle sort column click
     * @param {string} field - The field to sort by
     * @param {Array} sortableFields - Array of sortable field names
     */
    const handleSort = (field, sortableFields = []) => {
        // Skip if field is not provided or not in sortable fields
        if (!field || (sortableFields.length > 0 && !sortableFields.includes(field))) return;

        if (sort.value.field === field) {
            // Toggle direction if already sorting by this field
            sort.value.direction = sort.value.direction === 'asc' ? 'desc' : 'asc';
        } else {
            // Set new sort field and default to ascending
            sort.value.field = field;
            sort.value.direction = 'asc';
        }
        
        // Keep current page when sorting
        updateRoute();
    };

    /**
     * Handle pagination link clicks
     * @param {string} url - The URL to navigate to
     */
    const handlePageChange = (url) => {
        if (!url) return;
        
        // Extract page number from URL
        const urlObj = new URL(url);
        const pageParam = urlObj.searchParams.get('page');
        
        if (pageParam) {
            page.value = parseInt(pageParam);
            updateRoute();
        }
    };

    /**
     * Get the current sort icon for a field
     * @param {string} field - The field to check
     * @returns {string} - The sort direction or 'none'
     */
    const getSortIcon = (field) => {
        if (!field || sort.value.field !== field) return 'none';
        return sort.value.direction === 'asc' ? 'asc' : 'desc';
    };

    /**
     * Update filter parameters directly
     * @param {Object} filterParams - New filter values
     * @param {Boolean} resetPage - Whether to reset to page 1
     */
    const updateFilters = (filterParams, resetPage = true) => {
        updateRoute({
            ...filterParams,
            page: resetPage ? 1 : page.value
        });
    };

    return {
        // State
        page,
        search,
        sort,
        
        // Methods
        handlePageChange,
        handleSort,
        getSortIcon,
        updateRoute,
        updateFilters
    };
} 