<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import Button from '@/Components/Button.vue';
import Card from '@/Components/Card.vue';

const props = defineProps({
    featuredProperties: {
        type: Array,
        default: () => [],
    },
});

// Search form state
const searchForm = ref({
    location: '',
    check_in: '',
    check_out: '',
    guests: 1,
});

const handleSearch = () => {
    router.get(window.route('properties.index'), {
        city: searchForm.value.location,
        // Add date and guest filters later
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Find Your Perfect Shortlet - ShortletNG" />

        <!-- Hero Section with Search -->
        <section class="relative bg-gradient-to-br from-indigo-600 via-purple-600 to-pink-500 text-white overflow-hidden">
            <!-- Background Pattern -->
            <div class="absolute inset-0 bg-black/10"></div>
            <div class="absolute inset-0 bg-[url('/images/pattern.svg')] opacity-10"></div>

            <div class="relative mx-auto max-w-7xl px-4 py-24 sm:px-6 sm:py-32 lg:px-8">
                <div class="text-center">
                    <h1 class="text-5xl font-extrabold leading-tight tracking-tight sm:text-6xl lg:text-7xl">
                        Find Your Perfect
                        <span class="block bg-gradient-to-r from-yellow-300 to-orange-300 bg-clip-text text-transparent">
                            Shortlet
                        </span>
                    </h1>
                    <p class="mx-auto mt-6 max-w-2xl text-xl leading-relaxed text-indigo-100 sm:text-2xl">
                        Discover and book comfortable short-term rentals across Nigeria with ease
                    </p>
                </div>

                <!-- Search Form -->
                <div class="mx-auto mt-12 max-w-4xl">
                    <Card class="bg-white/95 backdrop-blur-sm shadow-2xl">
                        <form @submit.prevent="handleSearch" class="grid gap-4 p-6 md:grid-cols-4">
                            <!-- Location Input -->
                            <div class="md:col-span-2">
                                <label for="location" class="block text-sm font-medium text-gray-700 mb-1">
                                    Location
                                </label>
                                <input
                                    id="location"
                                    v-model="searchForm.location"
                                    type="text"
                                    placeholder="Where are you going?"
                                    class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                />
                            </div>

                            <!-- Guests Input -->
                            <div>
                                <label for="guests" class="block text-sm font-medium text-gray-700 mb-1">
                                    Guests
                                </label>
                                <input
                                    id="guests"
                                    v-model.number="searchForm.guests"
                                    type="number"
                                    min="1"
                                    class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                />
                            </div>

                            <!-- Search Button -->
                            <div class="flex items-end">
                                <Button type="submit" variant="primary" class="w-full">
                                    <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                    Search
                                </Button>
                            </div>
                        </form>
                    </Card>
                </div>
            </div>
        </section>

        <!-- Featured Properties Section -->
        <section v-if="featuredProperties.length > 0" class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
            <div class="text-center">
                <h2 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">
                    Featured Properties
                </h2>
                <p class="mt-2 text-lg text-gray-600">
                    Discover our handpicked selection of amazing shortlets
                </p>
            </div>

            <div class="mt-12 grid gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                <Link
                    v-for="property in featuredProperties"
                    :key="property.id"
                    :href="window.route('properties.show', property.id)"
                    class="group"
                >
                    <Card class="h-full overflow-hidden transition-all duration-300 hover:shadow-xl hover:-translate-y-1">
                        <!-- Image -->
                        <div class="relative aspect-[4/3] overflow-hidden bg-gray-200">
                            <img
                                v-if="property.images && property.images.length > 0"
                                :src="property.images[0].url"
                                :alt="property.title"
                                class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-110"
                            />
                            <div v-else class="flex h-full items-center justify-center bg-gradient-to-br from-gray-100 to-gray-200">
                                <svg class="h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                </svg>
                            </div>

                            <!-- Featured Badge -->
                            <div v-if="property.is_featured" class="absolute top-3 right-3">
                                <span class="inline-flex items-center rounded-full bg-yellow-400 px-3 py-1 text-xs font-semibold text-yellow-900 shadow-lg">
                                    ⭐ Featured
                                </span>
                            </div>
                        </div>

                        <!-- Content -->
                        <div class="p-4">
                            <h3 class="text-lg font-semibold text-gray-900 line-clamp-1">
                                {{ property.title }}
                            </h3>
                            <p class="mt-1 text-sm text-gray-600 line-clamp-1">
                                📍 {{ property.city }}, {{ property.state }}
                            </p>
                            <div class="mt-3 flex items-center justify-between">
                                <span class="text-xl font-bold text-indigo-600">
                                    ₦{{ Number(property.price_per_night).toLocaleString() }}
                                </span>
                                <span class="text-sm text-gray-500">
                                    per night
                                </span>
                            </div>
                            <div class="mt-3 flex items-center gap-3 text-sm text-gray-600">
                                <span>🛏️ {{ property.bedrooms }} beds</span>
                                <span>🚿 {{ property.bathrooms }} baths</span>
                                <span>👥 {{ property.max_guests }}</span>
                            </div>
                        </div>
                    </Card>
                </Link>
            </div>

            <div class="mt-12 text-center">
                <Link :href="window.route('properties.index')">
                    <Button variant="secondary" class="px-8">
                        View All Properties →
                    </Button>
                </Link>
            </div>
        </section>

        <!-- Features Section -->
        <section class="bg-gray-50 py-16">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="text-center">
                    <h2 class="text-3xl font-bold text-gray-900">Why Choose ShortletNG</h2>
                    <p class="mt-2 text-gray-600">Experience hassle-free short-term rentals</p>
                </div>

                <div class="mt-12 grid gap-8 md:grid-cols-3">
                    <Card class="text-center transition-shadow duration-300 hover:shadow-lg">
                        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-green-100">
                            <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="mt-4 text-xl font-semibold text-gray-900">
                            Verified Properties
                        </h3>
                        <p class="mt-2 text-gray-600">
                            All properties are verified for your safety and comfort
                        </p>
                    </Card>

                    <Card class="text-center transition-shadow duration-300 hover:shadow-lg">
                        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-indigo-100">
                            <svg class="h-8 w-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="mt-4 text-xl font-semibold text-gray-900">Secure Payments</h3>
                        <p class="mt-2 text-gray-600">
                            Safe and secure payment processing with trusted gateways
                        </p>
                    </Card>

                    <Card class="text-center transition-shadow duration-300 hover:shadow-lg">
                        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-purple-100">
                            <svg class="h-8 w-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="mt-4 text-xl font-semibold text-gray-900">
                            24/7 Support
                        </h3>
                        <p class="mt-2 text-gray-600">
                            Round-the-clock customer support for all your needs
                        </p>
                    </Card>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white">
            <div class="mx-auto max-w-7xl px-4 py-16 text-center sm:px-6 lg:px-8">
                <h2 class="text-3xl font-bold tracking-tight sm:text-4xl">Ready to Get Started?</h2>
                <p class="mt-4 text-lg text-indigo-100">
                    Join thousands of satisfied guests and hosts on ShortletNG
                </p>
                <div class="mt-8 flex justify-center gap-4">
                    <Link>
                        <Button variant="secondary" class="px-8">
                            Create Account
                        </Button>
                    </Link>
                    <Link>
                        <Button variant="ghost" class="border-2 border-white px-8 text-white hover:bg-white hover:text-indigo-600">
                            Browse Properties
                        </Button>
                    </Link>
                </div>
            </div>
        </section>
    </GuestLayout>
</template>
