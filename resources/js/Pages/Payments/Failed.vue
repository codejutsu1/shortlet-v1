<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <AppLayout>
        <div class="mx-auto max-w-2xl px-4 py-12 sm:px-6 lg:px-8">
            <!-- Error Icon -->
            <div class="mb-8 text-center">
                <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-red-100">
                    <svg class="h-12 w-12 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </div>
                <h1 class="mt-6 text-3xl font-bold text-gray-900">Payment Failed</h1>
                <p class="mt-2 text-gray-600">There was a problem processing your payment</p>
            </div>

            <!-- Error Details -->
            <div class="rounded-xl bg-white p-8 shadow-lg">
                <h2 class="mb-4 text-xl font-semibold">What happened?</h2>
                <p class="mb-6 text-gray-600">
                    Your payment could not be completed. This might be due to:
                </p>
                <ul class="mb-6 space-y-2 text-gray-600">
                    <li class="flex items-start">
                        <span class="mr-2">•</span>
                        <span>Insufficient funds in your account</span>
                    </li>
                    <li class="flex items-start">
                        <span class="mr-2">•</span>
                        <span>Card was declined by your bank</span>
                    </li>
                    <li class="flex items-start">
                        <span class="mr-2">•</span>
                        <span>Incorrect card details</span>
                    </li>
                    <li class="flex items-start">
                        <span class="mr-2">•</span>
                        <span>Network or technical issues</span>
                    </li>
                </ul>

                <div class="flex gap-3">
                    <Link
                        href="/bookings"
                        class="flex-1 rounded-lg bg-primary-600 px-6 py-3 text-center font-semibold text-white transition-colors hover:bg-primary-700"
                    >
                        View My Bookings
                    </Link>
                    <Link
                        href="/properties"
                        class="flex-1 rounded-lg border border-gray-300 px-6 py-3 text-center font-semibold text-gray-700 transition-colors hover:bg-gray-50"
                    >
                        Browse Properties
                    </Link>
                </div>
            </div>

            <p class="mt-6 text-center text-sm text-gray-500">
                Need help? Contact our support team
            </p>
        </div>
    </AppLayout>
</template>
