<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    title: {
        type: String,
        default: 'ShortletNG',
    },
});
</script>

<template>
    <div class="min-h-screen bg-gray-50">
        <!-- Header -->
        <header class="bg-white shadow-sm">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex h-16 items-center justify-between">
                    <!-- Logo -->
                    <Link href="/" class="text-2xl font-bold text-primary-600">
                        ShortletNG
                    </Link>

                    <!-- Navigation -->
                    <nav class="hidden space-x-8 md:flex">
                        <Link
                            href="/"
                            class="text-gray-700 transition-colors hover:text-primary-600"
                        >
                            Home
                        </Link>
                        <Link
                            href="/properties"
                            class="text-gray-700 transition-colors hover:text-primary-600"
                        >
                            Properties
                        </Link>
                        <Link
                            href="/bookings"
                            class="text-gray-700 transition-colors hover:text-primary-600"
                        >
                            My Bookings
                        </Link>
                    </nav>

                    <!-- User Menu -->
                    <div class="flex items-center space-x-4">
                        <Link
                            href="/profile"
                            class="text-gray-700 transition-colors hover:text-primary-600"
                        >
                            Profile
                        </Link>
                        <button
                            class="rounded-lg bg-primary-600 px-4 py-2 text-white transition-colors hover:bg-primary-700"
                        >
                            Logout
                        </button>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            <slot />
        </main>

        <!-- Footer -->
        <footer class="mt-auto bg-white">
            <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                <p class="text-center text-sm text-gray-500">
                    © 2025 ShortletNG. All rights reserved.
                </p>
            </div>
        </footer>
    </div>
</template>
