<script setup>
import { ref } from 'vue';
import {Link} from '@inertiajs/vue3'
import Table from '@/Components/Table.vue';
import TableDataCell from '@/Components/TableDataCell.vue';
import TableRow from '@/Components/TableRow.vue';
import TableHeaderCell from '@/Components/TableHeaderCell.vue';
import AuthLayout from '@/layouts/AuthLayout.vue';
import { format } from 'date-fns'; // You'll need to install date-fns
import Pagination from '@/Components/Pagination.vue';
const props = defineProps({
    activities: Object
});

defineOptions({
    layout: AuthLayout
});

const showDetails = ref(false);
const selectedActivity = ref(null);

const formatDate = (dateString) => {
    const date = new Date(dateString);
    return format(date, 'EEEE, MMMM d, yyyy - h:mm a'); 
};

const formatModelName = (modelType) => {
    if (!modelType) return 'System';
    return modelType.split('\\').pop(); 
};


</script>

<template>
    
        <Table>
            <thead>
                <tr>
                    <TableHeaderCell>Action</TableHeaderCell>
                    <TableHeaderCell>Performed By</TableHeaderCell>
                    <TableHeaderCell>Related To</TableHeaderCell>
                    <TableHeaderCell>Time</TableHeaderCell>
                    <TableHeaderCell>Details</TableHeaderCell>
                </tr>
            </thead>
            <tbody>
                <TableRow v-for="activity in props.activities.data" :key="activity.id">
                    <TableDataCell>
                        <span class="px-2 py-1 text-xs font-semibold rounded" 
                              :class="{
                                'bg-green-100 text-green-800': activity.description.includes('create'),
                                'bg-blue-100 text-blue-800': activity.description.includes('update'),
                                'bg-red-100 text-red-800': activity.description.includes('delete'),
                                
                              }">
                            {{ activity.description }}
                        </span>
                    </TableDataCell>
                    <TableDataCell>{{ activity.causer?.name ?? 'System' }}</TableDataCell>
                    <TableDataCell>
                        {{ formatModelName(activity.subject_type) }}s
                    </TableDataCell>
                    <TableDataCell>{{ formatDate(activity.created_at) }}</TableDataCell>
                    <TableDataCell>
                        <button  class="text-indigo-600 hover:text-indigo-800 hover:underline">
                            <Link 
                            :href="route('activity.view',activity.id)"
                            >
                            View Details
                            </Link>
                            
                        </button>
                    </TableDataCell>
                </TableRow>
            </tbody>
            <div class="flex items-center justify-between px-4">
            <div class="text-sm text-gray-700">
                Showing <span class="font-medium">{{ activities.from }}</span> to 
                <span class="font-medium">{{ activities.to }}</span> of 
                <span class="font-medium">{{ activities.total }}</span> results

                <Pagination :links="activities.links" />
            </div>
            
        </div>
        </Table>
          <!-- Pagination -->
        
        
    
</template>