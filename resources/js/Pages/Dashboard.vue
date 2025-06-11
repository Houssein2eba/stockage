<script setup>
import AuthenticatedLayout from '@/layouts/AuthLayout.vue';
import { Head } from '@inertiajs/vue3';
import Table from "@/Components/Table.vue";
import TableRow from "@/Components/TableRow.vue";
import TableHeaderCell from "@/Components/TableHeaderCell.vue";
import TableDataCell from "@/Components/TableDataCell.vue";
import {Link} from '@inertiajs/vue3';
import { useToast } from 'vue-toastification';
import { ref } from 'vue';
import { formatPrice } from '@/utils/format';

const props = defineProps({
    stats: {
        type: Object,
        required: true
    },
    chartData: {
        type: Array,
        required: true
    },
    recentSales: {
        type: Array,
        required: true
    },
});
</script>

<template>
    <Head title="Tableau de bord" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Tableau de bord
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Statistiques -->
                <div class="grid grid-cols-1 gap-6 mb-8 sm:grid-cols-2 lg:grid-cols-4">
                    <!-- Produits totaux -->
                    <div class="p-6 bg-white rounded-lg shadow-md">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-indigo-100">
                                <svg class="w-6 h-6 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Produits totaux</p>
                                <p class="text-2xl font-bold text-gray-700">{{ stats.totalProducts }}</p>
                            </div>
                        </div>
                        <div class="mt-4">
                            <a href="/products" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">Voir tous les produits</a>
                        </div>
                    </div>

                    <!-- Produits en rupture -->
                    <div class="p-6 bg-white rounded-lg shadow-md">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-red-100">
                                <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Produits en rupture</p>
                                <p class="text-2xl font-bold text-gray-700">{{ stats.lowStockCount }}</p>
                            </div>
                        </div>
                        <div class="mt-4">
                            <Link
                            class="text-sm font-medium text-red-600 hover:text-red-500"
                            :href="route('products.lowStock')"
                            >Voir les ruptures</Link>
                        </div>
                    </div>

                    <!-- Ventes totales -->
                    <div class="p-6 bg-white rounded-lg shadow-md">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-green-100">
                                <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Ventes totales</p>
                                <p class="text-2xl font-bold text-gray-700">{{ stats.totalSales }}</p>
                            </div>
                        </div>
                        <div class="mt-4">
                            <Link
                                class="text-sm font-medium text-green-600 hover:text-green-500"
                                :href="route('sales.index')"
                            >Voir toutes les ventes</Link>
                        </div>
                    </div>

                    <!-- Chiffre d'affaires -->
                    <div class="p-6 bg-white rounded-lg shadow-md">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-blue-100">
                                <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Chiffre d'affaires</p>
                                <p class="text-2xl font-bold text-gray-700">{{ formatPrice(stats.totalRevenue) }}</p>
                            </div>
                        </div>
                        <div class="mt-2">
                            <p class="text-sm font-medium text-gray-600">Bénéfice : {{ formatPrice(stats.totalProfit) }}</p>
                        </div>
                    </div>
                </div>

                <!-- Statistiques du jour -->
                <div class="grid grid-cols-1 gap-6 mb-8 sm:grid-cols-2">
                    <!-- Ventes du jour -->
                    <div class="p-6 bg-white rounded-lg shadow-md">
                        <h3 class="mb-4 text-lg font-semibold text-gray-700">Résumé du jour</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm font-medium text-gray-500">Ventes aujourd'hui</p>
                                <p class="text-xl font-bold text-green-700">{{ stats.todaySales }} </p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">CA aujourd'hui</p>
                                <p class="text-xl font-bold text-gray-700">{{ formatPrice(stats.todayRevenue) }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Statut des paiements -->
                    <div class="p-6 bg-white rounded-lg shadow-md">
                        <h3 class="mb-4 text-lg font-semibold text-gray-700">Statut des paiements</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm font-medium text-gray-500">Total payé</p>
                                <p class="text-xl font-bold text-green-600">{{ formatPrice(stats.paidAmount) }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Total dû</p>
                                <p class="text-xl font-bold text-red-600">{{ formatPrice(stats.dueAmount) }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Ventes récentes -->
                <div class="mb-8 bg-white rounded-lg shadow-md">
                    <div class="flex items-center justify-between px-6 py-4 border-b">
                        <h3 class="text-lg font-semibold text-gray-700">Ventes récentes</h3>
                        <Link :href="route('sales.index')" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">Voir tout</Link>
                    </div>
                    <div class="p-6">
                        <Table class="w-full">
                            <template #header>
                                <TableRow>
                                    <TableHeaderCell>Facture</TableHeaderCell>
                                    <TableHeaderCell>Client</TableHeaderCell>
                                    <TableHeaderCell>Date</TableHeaderCell>
                                    <TableHeaderCell>Montant</TableHeaderCell>
                                    <TableHeaderCell>Statut</TableHeaderCell>
                                </TableRow>
                            </template>
                            <template #body>
                                <TableRow v-for="sale in props.recentSales" :key="sale.id">
                                    <TableDataCell class="py-3">{{ sale.reference }}</TableDataCell>
                                    <TableDataCell class="py-3">
                                        <div class="truncate">
                                        <div class="text-sm font-medium text-gray-900">{{ sale.client?.name || 'Aucun client' }}</div>
                                        <div class="text-xs text-gray-400">{{ sale.client?.number || '--' }}</div>
                                    </div>
                                    </TableDataCell>
                                    <TableDataCell class="py-3">{{ new Date(sale.created_at).toLocaleDateString() }}</TableDataCell>
                                    <TableDataCell class="py-3">{{ formatPrice(sale.total_amount) }}</TableDataCell>
                                    <TableDataCell class="py-3">
                                        <span :class="{
                                            'px-2 py-1 text-xs font-medium rounded-full': true,
                                            'bg-green-100 text-green-800': sale.status === 'paid',
                                            'bg-yellow-100 text-yellow-800': sale.status === 'pending',
                                            'bg-red-100 text-red-800': sale.status === 'cancelled'
                                        }">
                                            {{
                                                sale.status === 'paid' ? 'Payé' :
                                                sale.status === 'pending' ? 'En attente' :
                                                'Annulé'
                                            }}
                                        </span>
                                    </TableDataCell>
                                </TableRow>
                            </template>
                        </Table>
                    </div>
                </div>

                <!-- Produits populaires -->
                <div class="mb-8 bg-white rounded-lg shadow-md">
                    <div class="flex items-center justify-between px-6 py-4 border-b">
                        <h3 class="text-lg font-semibold text-gray-700">Produits populaires</h3>
                        <a href="/sales" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">Voir tout</a>
                    </div>
                    <div class="p-6">
                        <Table class="w-full">
                            <template #header>
                                <TableRow>
                                    <TableHeaderCell>Nom</TableHeaderCell>
                                    <TableHeaderCell>Description</TableHeaderCell>
                                    <TableHeaderCell>Prix</TableHeaderCell>
                                    <TableHeaderCell>Vendus</TableHeaderCell>
                                    <TableHeaderCell>CA généré</TableHeaderCell>
                                </TableRow>
                            </template>
                            <template #body>
                                <TableRow v-for="product in props.stats.popular_products" :key="product.id">
                                    <TableDataCell class="py-3">{{ product.name }}</TableDataCell>
                                    <TableDataCell class="py-3">
                                        {{ product.description }}
                                    </TableDataCell>
                                    <TableDataCell class="py-3">{{ formatPrice(product.price) }} MRU</TableDataCell>
                                    <TableDataCell class="py-3">{{ product.total_quantity }}</TableDataCell>
                                    <TableDataCell class="py-3">{{ formatPrice(product.total_amount) }} MRU</TableDataCell>
                                </TableRow>
                            </template>
                        </Table>
                    </div>
                </div>

                <!-- Informations supplémentaires -->
                <div class="grid grid-cols-1 gap-6 mb-8 sm:grid-cols-3">
                    <div class="col-span-1 p-6 bg-white rounded-lg shadow-md sm:col-span-1">
                        <h3 class="mb-4 text-lg font-semibold text-gray-700">Catégories</h3>
                        <p class="text-2xl font-bold text-gray-700">{{ stats.totalCategories }}</p>
                        <Link :href="route('categories.index')" class="mt-2 text-sm font-medium text-indigo-600 hover:text-indigo-500">Gérer les catégories</Link>
                    </div>

                    <div class="col-span-1 p-6 bg-white rounded-lg shadow-md sm:col-span-1">
                        <h3 class="mb-4 text-lg font-semibold text-gray-700">Clients</h3>
                        <p class="text-2xl font-bold text-gray-700">{{ stats.clientCount }}</p>
                        <Link :href="route('clients.index')" class="mt-2 text-sm font-medium text-indigo-600 hover:text-indigo-500">Gérer les clients</Link>
                    </div>

                    <div class="col-span-1 p-6 bg-white rounded-lg shadow-md sm:col-span-2">
                        <h3 class="mb-4 text-lg font-semibold text-gray-700">Accès rapide</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <Link :href="route('products.create')" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700">Ajouter un produit</Link>
                            <Link :href="route('sales.create') " class="px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-md hover:bg-green-700">Nouvelle vente</Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
