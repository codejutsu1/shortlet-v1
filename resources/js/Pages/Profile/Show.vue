<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    user: Object,
    bookingsCount: Number,
    reviewsCount: Number,
});

const avatarUrl = computed(() => {
    return props.user.avatar 
        ? `/storage/${props.user.avatar}`
        : `https://ui-avatars.com/api/?name=${encodeURIComponent(props.user.name)}&background=6366f1&color=fff&size=200`;
});

const primaryImage = (property) => property.images?.find(img => img.is_primary)?.image_path 
    || property.images?.[0]?.image_path 
    || 'https://images.unsplash.com/photo-1580587771525-78b9dba3b914?w=400';
</script>

<template>
    <AppLayout>
        <div class="mx-auto max-w-6xl px-4 py-8 sm:px-6 lg:px-8">
            <!-- Profile Header -->
            <div class="mb-8 overflow-hidden rounded-xl bg-white shadow-md">
                <div class="p-6">
                    <div class="flex flex-col items-start gap-6 sm:flex-row sm:items-center">
                        <!-- Avatar -->
                        <div class="relative">
                            <img 
                                :src="avatarUrl" 
                                :alt="user.name"
                                class="h-24 w-24 rounded-full object-cover ring-4 ring-primary-100"
                            />
                        </div>

                        <!-- User Info -->
                        <div class="flex-1">
                            <h1 class="text-2xl font-bold text-gray-900">{{ user.name }}</h1>
                            <p class="mt-1 text-gray-600">{{ user.email }}</p>
                            <p v-if="user.phone" class="mt-1 text-gray-600">{{ user.phone }}</p>
                            
                            <!-- Stats -->
                            <div class="mt-4 flex gap-6">
                                <div>
                                    <span class="text-2xl font-bold text-primary-600">{{ bookingsCount }}</span>
                                    <span class="ml-1 text-gray-600">Bookings</span>
                                </div>
                                <div>
                                    <span class="text-2xl font-bold text-primary-600">{{ reviewsCount }}</span>
                                    <span class="ml-1 text-gray-600">Reviews</span>
                                </div>
                            </div>
                        </div>

                        <!-- Edit Button -->
                        <Link 
                            :href="route('profile.edit')"
                            class="rounded-lg bg-primary-600 px-6 py-3 font-semibold text-white transition-colors hover:bg-primary-700"
                        >
                            Edit Profile
                        </Link>
                    </div>
                </div>
            </div>

            <!-- Recent Bookings -->
            <div class="rounded-xl bg-white shadow-md">
                <div class="p-6">
                    <div class="mb-4 flex items-center justify-between">
                        <h2 class="text-xl font-semibold text-gray-900">Recent Bookings</h2>
                        <Link 
                            :href="route('bookings.index')"
                            class="text-primary-600 hover:text-primary-700"
                        >
                            View All →
                        </Link>
                    </div>
                    
                    <div v-if="user.bookings && user.bookings.length > 0" class="space-y-4">
                        <div 
                            v-for="booking in user.bookings" 
                            :key="booking.id"
                            class="overflow-hidden rounded-lg border border-gray-200 transition-shadow hover:shadow-md"
                        >
                            <div class="flex flex-col sm:flex-row">
                                <!-- Property Image -->
                                <div class="sm:w-32">
                                    <img 
                                        :src="primaryImage(booking.property)"
                                        :alt="booking.property.title"
                                        class="h-full w-full object-cover"
                                    />
                                </div>

                                <!-- Booking Info -->
                                <div class="flex-1 p-4">
                                    <h3 class="font-semibold text-gray-900">
                                        {{ booking.property.title }}
                                    </h3>
                                    <p class="text-sm text-gray-600">
                                        {{ new Date(booking.check_in).toLocaleDateString() }} - 
                                        {{ new Date(booking.check_out).toLocaleDateString() }}
                                    </p>
                                    <p class="mt-2 font-semibold text-primary-600">
                                        ₦{{ Number(booking.total_price).toLocaleString() }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div v-else class="py-12 text-center">
                        <p class="mb-4 text-gray-600">No bookings yet</p>
                        <Link
                            href="/properties"
                            class="inline-block rounded-lg bg-primary-600 px-6 py-3 font-semibold text-white transition-colors hover:bg-primary-700"
                        >
                            Browse Properties
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
