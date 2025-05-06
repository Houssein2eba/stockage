<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { useToast } from 'vue-toastification';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});
const toast = useToast();
const form = useForm({
    login: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onError: (errors) => {
            Object.keys(errors).forEach((key) => {
                toast.error(errors[key], {
                    position: 'top-right',
                    timeout: 5000,
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
                <h1 class="text-2xl font-bold text-gray-900">Welcome Back</h1>
                <p class="text-gray-600 mt-2">Please sign in to continue</p>
            </div>

            <div v-if="status" class="mb-4 p-4 rounded-lg bg-green-50 text-green-700">
                {{ status }}
            </div>

            <form @submit.prevent="submit" class="space-y-6">
                <div>
                    <InputLabel for="login" value="Email or phone" class="text-gray-700" />
                    <div class="mt-2 relative">
                        <TextInput
                            id="login"
                            type="text"
                            class="block w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            v-model="form.login"
                            required
                            autofocus
                            autocomplete="username"
                            :class="{ 'border-red-500 ring-red-500': form.errors.login }"
                        />
                        <InputError class="mt-1" :message="form.errors.login" />
                    </div>
                </div>

                <div>
                    <InputLabel for="password" value="Password" class="text-gray-700" />
                    <div class="mt-2 relative">
                        <TextInput
                            id="password"
                            type="password"
                            class="block w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
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
                        <Checkbox name="remember" v-model:checked="form.remember" class="text-blue-500 focus:ring-blue-500" />
                        <span class="ml-2 text-sm text-gray-600">Remember me</span>
                    </label>

                    <Link
                        v-if="canResetPassword"
                        :href="route('password.request')"
                        class="text-sm text-blue-600 hover:text-blue-800 hover:underline"
                    >
                        Forgot password?
                    </Link>
                </div>

                <PrimaryButton
                    class="w-full py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors"
                    :class="{ 'opacity-75 cursor-not-allowed': form.processing }"
                    :disabled="form.processing"
                >
                    <span v-if="form.processing">Signing in...</span>
                    <span v-else>Sign In</span>
                </PrimaryButton>

                <div class="text-center mt-4">
                    <span class="text-gray-600 text-sm">Don't have an account?</span>
                    <Link
                        :href="route('register')"
                        class="text-blue-600 hover:text-blue-800 text-sm ml-1 hover:underline"
                    >
                        Create account
                    </Link>
                </div>
            </form>
        </div>
    </GuestLayout>
</template>
