<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { useToast } from 'vue-toastification';
import { ref } from 'vue';

defineProps({
    canResetPassword: {
        type: Boolean,
        default: false,
    },
    status: {
        type: String,
        default: '',
    },
});

const toast = useToast();
const form = useForm({
    login: '',
    password: '',
    remember: false,
});

const passwordField = ref(null);

const submit = () => {
    form.post(route('login'), {
        onSuccess: () => {
            toast.success('Login successful!', {
                position: 'top-right',
                timeout: 3000,
            });
        },
        onError: (errors) => {
            if (errors.password) {
                passwordField.value.focus();
            }

            Object.values(errors).forEach((error) => {
                toast.error(error, {
                    position: 'top-right',
                    timeout: 5000,
                    closeOnClick: true,
                    pauseOnFocusLoss: true,
                    pauseOnHover: true,
                });
            });
        },
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Log in" />

        <div class="w-full max-w-md mx-auto p-6">
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">Welcome Back</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-2">Please login to continue</p>
            </div>

            <div
                v-if="status"
                class="mb-4 p-4 rounded-lg bg-green-50 dark:bg-green-900/30 text-green-700 dark:text-green-300"
            >
                {{ status }}
            </div>

            <form @submit.prevent="submit" class="space-y-6">
                <div>
                    <InputLabel for="login" value="Email or Phone" class="text-gray-700 dark:text-gray-300" />
                    <div class="mt-2 relative">
                        <TextInput
                            id="login"
                            type="text"
                            class="block w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                            v-model="form.login"
                            required
                            
                            autocomplete="username"
                            :class="{ 'border-red-500 ring-red-500': form.errors.login }"
                        />
                        <InputError class="mt-1" :message="form.errors.login" />
                    </div>
                </div>

                <div>
                    <InputLabel for="password" value="Password" class="text-gray-700 dark:text-gray-300" />
                    <div class="mt-2 relative">
                        <TextInput
                            id="password"
                            ref="passwordField"
                            type="password"
                            class="block w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                            v-model="form.password"
                            required
                            autocomplete="current-password"
                            :class="{ 'border-red-500 ring-red-500': form.errors.password }"
                        />
                        <InputError class="mt-1" :message="form.errors.password" />
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <label class="flex items-center">
                        <Checkbox
                            name="remember"
                            v-model:checked="form.remember"
                            class="ring-2 text-blue-500 ring-blue-500 dark:focus:ring-blue-600 dark:border-gray-600"
                        />
                        <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">Remember me</span>
                    </label>

                    <Link
                        v-if="canResetPassword"
                        :href="route('password.request')"
                        class="text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 hover:underline"
                    >
                        Forgot password?
                    </Link>
                </div>

                <div class="mt-6">
    <PrimaryButton
        class="w-full py-3 bg-blue-600 hover:bg-blue-700 dark:bg-blue-700 dark:hover:bg-blue-800 text-white rounded-lg transition-colors focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 flex justify-center items-center"
        :class="{ 'opacity-75 cursor-not-allowed': form.processing }"
        :disabled="form.processing"
    >
        <template v-if="form.processing">
            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span>Logging in...</span>
        </template>
        <span v-else>Log In</span>
    </PrimaryButton>
</div>
            </form>
        </div>
    </GuestLayout>
</template>
