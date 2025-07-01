<script setup>
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
    laravelVersion: {
        type: String,
        required: true,
    },
    phpVersion: {
        type: String,
        required: true,
    },
});

function handleImageError() {
    document.getElementById('screenshot-container')?.classList.add('!hidden');
    document.getElementById('docs-card')?.classList.add('!row-span-1');
    document.getElementById('docs-card-content')?.classList.add('!flex-row');
    document.getElementById('background')?.classList.add('!hidden');
}
</script>

<template>
    <Head title="Bienvenue dans le Gestionnaire de Stock" />
    <div class="bg-gray-50 text-black/50 dark:bg-black dark:text-white/50">
        <div class="relative flex min-h-screen flex-col items-center justify-center selection:bg-blue-500 selection:text-white">
            <div class="relative w-full max-w-2xl px-6 lg:max-w-7xl">
                <header class="grid grid-cols-2 items-center gap-2 py-10 lg:grid-cols-3">
                    <div class="flex lg:col-start-2 lg:justify-center">
                        <svg class="h-12 w-auto text-blue-600 lg:h-16" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                    </div>
                    <nav v-if="canLogin" class="-mx-3 flex flex-1 justify-end">
                        <Link
                            v-if="$page.props.auth.user"
                            :href="route('dashboard')"
                            class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-blue-500 dark:text-white dark:hover:text-white/80"
                        >
                            Tableau de bord
                        </Link>

                        <template v-else>
                            <Link
                                :href="route('login')"
                                class="rounded-md px-4 py-2 bg-blue-600 text-white hover:bg-blue-700 transition flex items-center gap-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M3 3a1 1 0 011 1v12a1 1 0 11-2 0V4a1 1 0 011-1zm7.707 3.293a1 1 0 010 1.414L9.414 9H17a1 1 0 110 2H9.414l1.293 1.293a1 1 0 01-1.414 1.414l-3-3a1 1 0 010-1.414l3-3a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                                Connexion
                            </Link>
                        </template>
                    </nav>
                </header>

                <main class="mt-6">
                    <div class="text-center mb-12">
                        <h1 class="text-4xl font-bold text-black dark:text-white mb-4">
                            Système de Gestion de Stock
                        </h1>
                        <p class="text-xl text-gray-600 dark:text-gray-400">
                            Gérez efficacement votre inventaire, suivez vos produits et traitez vos commandes
                        </p>
                    </div>

                    <div class="grid gap-6 lg:grid-cols-2 lg:gap-8">
                        <div class="flex flex-col items-start gap-6 rounded-lg bg-white p-6 shadow-lg dark:bg-zinc-900">
                            <div class="flex size-12 shrink-0 items-center justify-center rounded-full bg-blue-100 dark:bg-blue-900">
                                <svg class="size-6 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-xl font-semibold text-black dark:text-white">Gestion d'Inventaire</h2>
                                <p class="mt-4 text-gray-600 dark:text-gray-400">
                                    Suivez vos produits, surveillez les niveaux de stock et recevez des alertes pour les stocks bas.
                                </p>
                            </div>
                        </div>

                        <div class="flex flex-col items-start gap-6 rounded-lg bg-white p-6 shadow-lg dark:bg-zinc-900">
                            <div class="flex size-12 shrink-0 items-center justify-center rounded-full bg-blue-100 dark:bg-blue-900">
                                <svg class="size-6 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-xl font-semibold text-black dark:text-white">Gestion des  Ventes</h2>
                                <p class="mt-4 text-gray-600 dark:text-gray-400">
                                    Traitez les ventes efficacement, suivez leur statut et gérez les informations clients.
                                </p>
                            </div>
                        </div>

                        <div class="flex flex-col items-start gap-6 rounded-lg bg-white p-6 shadow-lg dark:bg-zinc-900">
                            <div class="flex size-12 shrink-0 items-center justify-center rounded-full bg-blue-100 dark:bg-blue-900">
                                <svg class="size-6 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-xl font-semibold text-black dark:text-white">Analyses & Rapports</h2>
                                <p class="mt-4 text-gray-600 dark:text-gray-400">
                                    Générez des rapports détaillés, analysez les tendances de vente et prenez des décisions éclairées.
                                </p>
                            </div>
                        </div>

                        <div class="flex flex-col items-start gap-6 rounded-lg bg-white p-6 shadow-lg dark:bg-zinc-900">
                            <div class="flex size-12 shrink-0 items-center justify-center rounded-full bg-blue-100 dark:bg-blue-900">
                                <svg class="size-6 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-xl font-semibold text-black dark:text-white">Gestion des Utilisateurs</h2>
                                <p class="mt-4 text-gray-600 dark:text-gray-400">
                                    Gérez les rôles et permissions des utilisateurs pour un accès sécurisé aux fonctionnalités.
                                </p>
                            </div>
                        </div>
                    </div>
                </main>

                <footer class="py-16 text-center text-sm text-black dark:text-white/70">
                    Système de Gestion de Stock
                </footer>
            </div>
        </div>
    </div>
</template>
