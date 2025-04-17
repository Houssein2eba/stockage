<script setup>
import { usePermission } from '@/composables/permissions';
import { useAdmin } from '@/composables/admins';
import AuthLayout from '@/layouts/AuthLayout.vue';
import { Head } from '@inertiajs/vue3';
import {Link} from '@inertiajs/vue3';

const { hasPermission } = usePermission();
const {hasRole} = usePermission();
const {isAdmin} = useAdmin();
</script>

<template>
    <Head title="Dashboard" />

    <AuthLayout>
        <template #header>
            <h2
                class="text-xl font-semibold leading-tight text-gray-800"
            >
                Dashboard
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div
                    class="overflow-hidden bg-white shadow-sm sm:rounded-lg"
                >
                    <div class="p-6 text-gray-900">
                        You're logged in!
                        <span >{{ $page.props.auth.roles }}-{{ isAdmin }}</span>
                        <Link
                        :href="route('users.index')"
                        >
                    Index
                     </Link>
                    </div>
                </div>
            </div>
        </div>
    </AuthLayout>
</template>
