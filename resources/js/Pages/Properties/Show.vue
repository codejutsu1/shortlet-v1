<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    property: {
        type: Object,
        required: true,
    },
    similarProperties: {
        type: Array,
        default: () => [],
    },
});

const primaryImage = property => property.images?.find(img => img.is_primary)?.image_path 
    || property.images?.[0]?.image_path 
    || 'https://images.unsplash.com/photo-1580587771525-78b9dba3b914?w=1200';
</script>

<template>
    <div class="min-h-screen bg-gray-50">
        <!-- Header with back button -->
        <div class="bg-white shadow-sm">
            <div class="mx-auto max-w-7xl px-4 py-4 sm:px-6 lg:px-8">
                <Link href="/properties" class="inline-flex items-center text-primary-600 hover:text-primary-700">
                    <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Back to Properties
                </Link>
            </div>
        </div>

        <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            <!-- Property Images -->
            <div class="mb-8 grid gap-4 md:grid-cols-2">
                <div class="md:col-span-2">
                    <img 
                        :src="primaryImage(property)"
                        :alt="property.title"
                        class="h-96 w-full rounded-xl object-cover shadow-lg"
                    />
                </div>
                <template v-if="property.images && property.images.length > 1">
                    <img 
                        v-for="(image, index) in property.images.slice(1, 3)" 
                        :key="index"
                        :src="image.image_path"
                        class="h-48 w-full rounded-xl object-cover shadow-md"
                    />
                </template>
            </div>

            <div class="grid gap-8 lg:grid-cols-3">
                <!-- Main Content -->
                <div class="lg:col-span-2">
                    <!-- Title & Location -->
                    <div class="mb-6">
                        <h1 class="mb-2 text-3xl font-bold text-gray-900">{{ property.title }}</h1>
                        <p class="text-lg text-gray-600">{{ property.city }}, {{ property.state }}</p>
                    </div>

                    <!-- Property Stats -->
                    <div class="mb-6 flex flex-wrap gap-6 rounded-xl bg-white p-6 shadow-md">
                        <div>
                            <p class="text-sm text-gray-600">Bedrooms</p>
                            <p class="text-xl font-semibold">{{ property.bedrooms }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Bathrooms</p>
                            <p class="text-xl font-semibold">{{ property.bathrooms }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Max Guests</p>
                            <p class="text-xl font-semibold">{{ property.max_guests }}</p>
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="mb-6 rounded-xl bg-white p-6 shadow-md">
                        <h2 class="mb-4 text-xl font-semibold">About this property</h2>
                        <p class="whitespace-pre-line text-gray-700">{{ property.description }}</p>
                    </div>

                    <!-- Amenities -->
                    <div v-if="property.amenities && property.amenities.length > 0" class="rounded-xl bg-white p-6 shadow-md">
                        <h2 class="mb-4 text-xl font-semibold">Amenities</h2>
                        <div class="grid gap-3 sm:grid-cols-2">
                            <div 
                                v-for="amenity in property.amenities" 
                                :key="amenity.id"
                                class="flex items-center"
                            >
                                <svg class="mr-2 h-5 w-5 text-primary-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                                <span class="text-gray-700">{{ amenity.name }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Booking Card (Sticky) -->
                <div class="lg:col-span-1">
                    <div class="sticky top-4 rounded-xl bg-white p-6 shadow-lg">
                        <div class="mb-4 text-center">
                            <span class="text-3xl font-bold text-gray-900">
                                ₦{{ Number(property.price_per_night).toLocaleString() }}
                            </span>
                            <span class="text-gray-600">/night</span>
                        </div>

                        <p class="mb-4 text-center text-sm text-gray-600">
                            Sign in to book this property
                        </p>

                        <Link
                            href="/login"
                            class="block w-full rounded-lg bg-primary-600 px-6 py-3 text-center font-semibold text-white transition-colors hover:bg-primary-700"
                        >
                            Login to Book
                        </Link>

                        <p class="mt-4 text-center text-xs text-gray-500">
                            You won't be charged yet
                        </p>
                    </div>
                </div>
            </div>

            <!-- Similar Properties -->
            <div v-if="similarProperties.length > 0" class="mt-12">
                <h2 class="mb-6 text-2xl font-bold text-gray-900">Similar Properties</h2>
                <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                    <Link 
                        v-for="similar in similarProperties" 
                        :key="similar.id"
                        :href="`/properties/${similar.id}`"
                        class="group overflow-hidden rounded-xl bg-white shadow-md transition-shadow hover:shadow-xl"
                    >
                        <div class="aspect-[4/3]">
                            <img 
                                :src="primaryImage(similar)"
                                :alt="similar.title"
                                class="h-full w-full object-cover transition-transform group-hover:scale-110"
                            />
                        </div>
                        <div class="p-4">
                            <h3 class="mb-1 font-semibold text-gray-900 line-clamp-1">{{ similar.title }}</h3>
                            <p class="mb-2 text-sm text-gray-600">{{ similar.city }}</p>
                            <p class="font-bold text-primary-600">₦{{ Number(similar.price_per_night).toLocaleString() }}/night</p>
                        </div>
                    </Link>
                </div>
            </div>
        </div>
    </div>
</template>
