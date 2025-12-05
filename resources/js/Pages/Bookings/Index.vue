<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';

defineProps({
    bookings: Array,
});

const getStatusColor = (status) => {
    const colors = {
        pending: 'bg-yellow-100 text-yellow-800',
        confirmed: 'bg-green-100 text-green-800',
        completed: 'bg-blue-100 text-blue-800',
        cancelled: 'bg-red-100 text-red-800',
    };
    return colors[status] || 'bg-gray-100 text-gray-800';
};

const primaryImage = (property) => property.images?.find(img => img.is_primary)?.image_path 
    || property.images?.[0]?.image_path 
    || 'https://images.unsplash.com/photo-1580587771525-78b9dba3b914?w=400';
</script>

<template>
    <AppLayout>
        <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            <h1 class="mb-8 text-3xl font-bold text-gray-900">My Bookings</h1>

            <div v-if="bookings.length > 0" class="space-y-4">
                <div 
                    v-for="booking in bookings" 
                    :key="booking.id"
                    class="overflow-hidden rounded-xl bg-white shadow-md transition-shadow hover:shadow-lg"
                >
                    <div class="flex flex-col md:flex-row">
                        <!-- Property Image -->
                        <div class="md:w-48">
                            <img 
                                :src="primaryImage(booking.property)"
                                :alt="booking.property.title"
                                class="h-full w-full object-cover"
                            />
                        </div>

                        <!-- Booking Details -->
                        <div class="flex-1 p-6">
                            <div class="flex items-start justify-between">
                                <div>
                                    <h3 class="text-xl font-semibold text-gray-900">
                                        {{ booking.property.title }}
                                    </h3>
                                    <p class="mt-1 text-gray-600">
                                        {{ booking.property.city }}, {{ booking.property.state }}
                                    </p>
                                </div>
                                <span 
                                    :class="getStatusColor(booking.status)"
                                    class="rounded-full px-3 py-1 text-sm font-semibold"
                                >
                                    {{ booking.status.charAt(0).toUpperCase() + booking.status.slice(1) }}
                                </span>
                            </div>

                            <div class="mt-4 grid gap-4 sm:grid-cols-3">
                                <div>
                                    <p class="text-sm text-gray-600">Check-in</p>
                                    <p class="font-semibold">{{ new Date(booking.check_in).toLocaleDateString() }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Check-out</p>
                                    <p class="font-semibold">{{ new Date(booking.check_out).toLocaleDateString() }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Guests</p>
                                    <p class="font-semibold">{{ booking.guests }}</p>
                                </div>
                            </div>

                            <div class="mt-4 flex items-center justify-between gap-4">
                                <div>
                                    <p class="text-sm text-gray-600">Total Price</p>
                                    <p class="text-2xl font-bold text-primary-600">
                                        ₦{{ Number(booking.total_price).toLocaleString() }}
                                    </p>
                                </div>
                                <div class="flex gap-3">
                                    <!-- Leave Review Button (for completed bookings without review) -->
                                    <Link
                                        v-if="booking.status === 'completed' && !booking.review && new Date(booking.check_out) < new Date()"
                                        :href="`/bookings/${booking.id}/review`"
                                        class="rounded-lg border-2 border-primary-600 px-6 py-2 font-semibold text-primary-600 transition-colors hover:bg-primary-50"
                                    >
                                        Leave a Review
                                    </Link>
                                    <!-- Already Reviewed Indicator -->
                                    <span 
                                        v-else-if="booking.review"
                                        class="flex items-center gap-1 rounded-lg bg-green-50 px-4 py-2 text-green-700"
                                    >
                                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                        </svg>
                                        <span class="text-sm font-semibold">Reviewed</span>
                                    </span>
                                    <!-- View Details Button -->
                                    <Link
                                        :href="`/bookings/${booking.id}`"
                                        class="rounded-lg bg-primary-600 px-6 py-2 font-semibold text-white transition-colors hover:bg-primary-700"
                                    >
                                        View Details
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Empty State -->
            <div v-else class="rounded-xl bg-white p-12 text-center shadow-md">
                <p class="mb-4 text-lg text-gray-600">You don't have any bookings yet.</p>
                <Link
                    href="/properties"
                    class="inline-block rounded-lg bg-primary-600 px-6 py-3 font-semibold text-white transition-colors hover:bg-primary-700"
                >
                    Browse Properties
                </Link>
            </div>
        </div>
    </AppLayout>
</template>
