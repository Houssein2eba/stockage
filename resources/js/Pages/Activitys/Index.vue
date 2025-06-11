<script setup>
import { ref, computed, watch } from 'vue';
import { Link, Head, router } from '@inertiajs/vue3';
import Table from '@/Components/Table.vue';
import TableDataCell from '@/Components/TableDataCell.vue';
import TableRow from '@/Components/TableRow.vue';
import TableHeaderCell from '@/Components/TableHeaderCell.vue';
import AuthLayout from '@/layouts/AuthLayout.vue';
import { format } from 'date-fns';
import Pagination from '@/Components/Pagination.vue';

import DatePicker from 'primevue/datepicker';

const props = defineProps({
    activities: Object,
    dates: String
});

defineOptions({
    layout: AuthLayout
});

const searchQuery = ref('');

const filteredActivities = computed(() => {
    if (!searchQuery.value) return props.activities.data;
    return props.activities.data.filter(activity =>
        activity.description.toLowerCase().includes(searchQuery.value.toLowerCase())
    );
});
const date = ref(props.dates);
const formatDate = (dateString) => {
    const date = new Date(dateString);
    return format(date, 'EEEE, d MMMM yyyy - HH:mm');
};


watch(date, (newDate) => {
    const formattedDate = format(newDate, 'yyyy-MM-dd');
    router.get(`/activity`, { dates: formattedDate }, { preserveState: true, preserveScroll: true });
});
const formatModelName = (modelType) => {
    if (!modelType) return 'Système';
    return modelType.split('\\').pop();
};

</script>

<template>
    <Head title="Journal d'activité" />

        <H1>Journal des activités</H1>

        <div class="mb-4 w-1/2">
            <h2>Sélectionner une date</h2>
            <DatePicker v-model="date" />
        </div>

        <Table class="w-full">
            <template #header>
                <tr>
                    <TableHeaderCell>Action</TableHeaderCell>
                    <TableHeaderCell>Effectué par</TableHeaderCell>
                    <TableHeaderCell>Relatif à</TableHeaderCell>
                    <TableHeaderCell>Date et heure</TableHeaderCell>
                    <!-- <TableHeaderCell>Détails</TableHeaderCell> -->
                </tr>
            </template>
            <template #body>
                <TableRow v-for="activity in filteredActivities" :key="activity.id">
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
                    <TableDataCell>{{ activity.causer?.name ?? 'Système' }}</TableDataCell>
                    <TableDataCell>
                        {{ formatModelName(activity.subject_type) === 'Order' ? 'Ventes' : formatModelName(activity.subject_type) }}

                    </TableDataCell>
                    <TableDataCell>{{ formatDate(activity.created_at) }}</TableDataCell>
                    <!-- <TableDataCell>
                        <Link
                            :href="route('activity.view', activity.id)"
                            class=" hover:text-green-600 hover:underline">
                            Voir détails
                        </Link>
                    </TableDataCell> -->
                </TableRow>
            </template>
        </Table>
        <!-- Pagination  -->
        <div class="flex items-center justify-between mt-4">
            <div class="text-sm text-gray-700">
                Affichage de <span class="font-medium">{{ activities.from }}</span> à
                <span class="font-medium">{{ activities.to }}</span> sur
                <span class="font-medium">{{ activities.total }}</span> résultats
            </div>
            <Pagination :links="activities.links" />
        </div>
</template>
