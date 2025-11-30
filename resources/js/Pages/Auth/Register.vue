<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import Button from '@/Components/Button.vue';
import Input from '@/Components/Input.vue';
import { useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout>
        <div class="flex min-h-[calc(100vh-16rem)] items-center justify-center px-4 py-12">
            <div class="w-full max-w-md">
                <!-- Registration Card -->
                <div class="rounded-xl bg-white p-8 shadow-lg">
                    <div class="mb-8 text-center">
                        <h1 class="text-3xl font-bold text-gray-900">Create Account</h1>
                        <p class="mt-2 text-gray-600">Join ShortletNG today</p>
                    </div>

                    <!-- Google OAuth Button -->
                    <a
                        :href="route('auth.google')"
                        class="mb-6 flex w-full items-center justify-center gap-3 rounded-lg border-2 border-gray-300 bg-white px-6 py-3 font-semibold text-gray-700 transition-all duration-200 hover:border-gray-400 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"
                    >
                        <!-- Google Logo SVG -->
                        <svg class="h-5 w-5" viewBox="0 0 24 24">
                            <path
                                fill="#4285F4"
                                d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"
                            />
                            <path
                                fill="#34A853"
                                d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"
                            />
                            <path
                                fill="#FBBC05"
                                d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"
                            />
                            <path
                                fill="#EA4335"
                                d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"
                            />
                        </svg>
                        Sign up with Google
                    </a>

                    <!-- Divider -->
                    <div class="relative mb-6">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-gray-300"></div>
                        </div>
                        <div class="relative flex justify-center text-sm">
                            <span class="bg-white px-4 text-gray-500">Or sign up with email</span>
                        </div>
                    </div>

                    <!-- Registration Form -->
                    <form @submit.prevent="submit">
                        <!-- Name Input -->
                        <div class="mb-4">
                            <label for="name" class="mb-2 block text-sm font-medium text-gray-700">
                                Full Name
                            </label>
                            <Input
                                id="name"
                                v-model="form.name"
                                type="text"
                                required
                                placeholder="John Doe"
                            />
                            <div v-if="form.errors.name" class="mt-1 text-sm text-red-600">
                                {{ form.errors.name }}
                            </div>
                        </div>

                        <!-- Email Input -->
                        <div class="mb-4">
                            <label for="email" class="mb-2 block text-sm font-medium text-gray-700">
                                Email Address
                            </label>
                            <Input
                                id="email"
                                v-model="form.email"
                                type="email"
                                required
                                placeholder="you@example.com"
                            />
                            <div v-if="form.errors.email" class="mt-1 text-sm text-red-600">
                                {{ form.errors.email }}
                            </div>
                        </div>

                        <!-- Password Input -->
                        <div class="mb-4">
                            <label for="password" class="mb-2 block text-sm font-medium text-gray-700">
                                Password
                            </label>
                            <Input
                                id="password"
                                v-model="form.password"
                                type="password"
                                required
                                placeholder="Create a strong password"
                            />
                            <div v-if="form.errors.password" class="mt-1 text-sm text-red-600">
                                {{ form.errors.password }}
                            </div>
                        </div>

                        <!-- Password Confirmation Input -->
                        <div class="mb-6">
                            <label for="password_confirmation" class="mb-2 block text-sm font-medium text-gray-700">
                                Confirm Password
                            </label>
                            <Input
                                id="password_confirmation"
                                v-model="form.password_confirmation"
                                type="password"
                                required
                                placeholder="Confirm your password"
                            />
                        </div>

                        <!-- Submit Button -->
                        <Button type="submit" variant="primary" class="w-full" :disabled="form.processing">
                            {{ form.processing ? 'Creating account...' : 'Create Account' }}
                        </Button>
                    </form>

                    <!-- Login Link -->
                    <div class="mt-6 text-center text-sm text-gray-600">
                        Already have an account?
                        <a href="/login" class="font-semibold text-primary-600 hover:text-primary-700">
                            Sign in
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </GuestLayout>
</template>
