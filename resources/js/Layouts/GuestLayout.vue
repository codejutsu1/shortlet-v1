<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="min-h-screen bg-gray-50">
        <!-- Simple Header -->
        <header class="bg-white shadow-sm">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex h-16 items-center justify-between">
                    <!-- Logo -->
                    <Link href="/" class="text-2xl font-bold text-primary-600">
                        ShortletNG
                    </Link>

                    <!-- Auth Links -->
                    <div class="flex items-center space-x-4">
                        <Link
                            href="/login"
                            class="text-gray-700 transition-colors hover:text-primary-600"
                        >
                            Login
                        </Link>
                        <Link
                            href="/register"
                            class="rounded-lg bg-primary-600 px-4 py-2 text-white transition-colors hover:bg-primary-700"
                        >
                            Sign Up
                        </Link>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main>
            <slot />
        </main>

        <!-- Footer -->
        <footer class="mt-16 bg-gray-900 text-white">
            <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 gap-8 md:grid-cols-3">
                    <!-- About -->
                    <div>
                        <h3 class="mb-4 text-lg font-semibold">ShortletNG</h3>
                        <p class="text-sm text-gray-400">
                            Your trusted platform for short-term rentals in Nigeria.
                        </p>
                    </div>

                    <!-- Quick Links -->
                    <div>
                        <h4 class="mb-4 text-lg font-semibold">Quick Links</h4>
                        <ul class="space-y-2 text-sm text-gray-400">
                            <li>
                                <Link href="/" class="hover:text-white">Home</Link>
                            </li>
                            <li>
                                <Link href="/properties" class="hover:text-white"
                                    >Properties</Link
                                >
                            </li>
                            <li>
                                <Link href="/about" class="hover:text-white">About Us</Link>
                            </li>
                        </ul>
                    </div>

                    <!-- Contact -->
                    <div>
                        <h4 class="mb-4 text-lg font-semibold">Contact</h4>
                        <p class="text-sm text-gray-400">
                            Email: info@shortletng.com<br />
                            Phone: +234 xxx xxx xxxx
                        </p>
                    </div>
                </div>

                <div class="mt-8 border-t border-gray-800 pt-8 text-center text-sm text-gray-400">
                    © 2025 ShortletNG. All rights reserved.
                </div>
            </div>
        </footer>
    </div>
</template>
