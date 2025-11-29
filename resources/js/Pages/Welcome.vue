<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import Button from '@/Components/Button.vue';
import Card from '@/Components/Card.vue';
import Badge from '@/Components/Badge.vue';
import { Head } from '@inertiajs/vue3';
</script>

<template>
    <GuestLayout>
        <Head title="Welcome to ShortletNG" />

        <!-- Hero Section -->
        <section class="bg-primary-600 text-white">
            <div class="mx-auto max-w-7xl px-4 py-20 sm:px-6 lg:px-8">
                <div class="text-center">
                    <h1 class="text-5xl font-extrabold leading-tight">
                        Find Your Perfect Shortlet
                    </h1>
                    <p class="mx-auto mt-4 max-w-2xl text-xl">
                        Discover and book comfortable short-term rentals across Nigeria
                    </p>
                    <div class="mt-8 flex justify-center gap-4">
                        <Button variant="secondary">Browse Properties</Button>
                        <Button variant="ghost">Learn More</Button>
                    </div>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
            <div class="text-center">
                <h2 class="text-3xl font-bold text-gray-900">Why Choose ShortletNG</h2>
                <p class="mt-2 text-gray-600">Experience hassle-free short-term rentals</p>
            </div>

            <div class="mt-12 grid grid-cols-1 gap-8 md:grid-cols-3">
                <Card>
                    <div class="text-center">
                        <div
                            class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-primary-100"
                        >
                            <svg
                                class="h-8 w-8 text-primary-600"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                                />
                            </svg>
                        </div>
                        <h3 class="mt-4 text-xl font-semibold text-gray-900">
                            Verified Properties
                        </h3>
                        <p class="mt-2 text-gray-600">
                            All properties are verified for your safety and comfort
                        </p>
                        <Badge variant="success" class="mt-4">Verified</Badge>
                    </div>
                </Card>

                <Card>
                    <div class="text-center">
                        <div
                            class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-secondary-100"
                        >
                            <svg
                                class="h-8 w-8 text-secondary-600"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                                />
                            </svg>
                        </div>
                        <h3 class="mt-4 text-xl font-semibold text-gray-900">Secure Payments</h3>
                        <p class="mt-2 text-gray-600">
                            Safe and secure payment processing with trusted gateways
                        </p>
                        <Badge variant="info" class="mt-4">Secure</Badge>
                    </div>
                </Card>

                <Card>
                    <div class="text-center">
                        <div
                            class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-yellow-100"
                        >
                            <svg
                                class="h-8 w-8 text-yellow-600"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                                />
                            </svg>
                        </div>
                        <h3 class="mt-4 text-xl font-semibold text-gray-900">
                            24/7 Support
                        </h3>
                        <p class="mt-2 text-gray-600">
                            Round-the-clock customer support for all your needs
                        </p>
                        <Badge variant="warning" class="mt-4">Available</Badge>
                    </div>
                </Card>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="bg-gray-100">
            <div class="mx-auto max-w-7xl px-4 py-16 text-center sm:px-6 lg:px-8">
                <h2 class="text-3xl font-bold text-gray-900">Ready to Get Started?</h2>
                <p class="mt-2 text-gray-600">
                    Join thousands of satisfied guests and hosts on ShortletNG
                </p>
                <div class="mt-8">
                    <Button variant="primary">Create Account</Button>
                </div>
            </div>
        </section>
    </GuestLayout>
</template>
