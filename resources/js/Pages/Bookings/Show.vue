<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    booking: Object,
});

const cancelForm = useForm({});

const cancelBooking = () => {
    if (confirm('Are you sure you want to cancel this booking?')) {
        cancelForm.post(route('bookings.cancel', props.booking.id));
    }
};

const getStatusColor = (status) => {
    const colors = {
        pending: 'bg-yellow-100 text-yellow-800',
        confirmed: 'bg-green-100 text-green-800',
        completed: 'bg-blue-100 text-blue-800',
        cancelled: 'bg-red-100 text-red-800',
    };
    return colors[status] || 'bg-gray-100 text-gray-800';
};

const primaryImage = booking.property.images?.find(img => img.is_primary)?.image_path 
    || booking.property.images?.[0]?.image_path 
    || 'https://images.unsplash.com/photo-1580587771525-78b9dba3b914?w=800';
</script>

<template>
    <AppLayout>
        <div class="mx-auto max-w-4xl px-4 py-8 sm:px-6 lg:px-8">
            <!-- Back Button -->
            <Link href="/bookings" class="mb-6 inline-flex items-center text-primary-600 hover:text-primary-700">
                <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Back to Bookings
            </Link>

            <div class="rounded-xl bg-white p-8 shadow-lg">
                <!-- Header -->
                <div class="mb-6 flex items-start justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Booking Details</h1>
                        <p class="mt-1 text-gray-600">Booking #{{ booking.id }}</p>
                    </div>
                    <span 
                        :class="getStatusColor(booking.status)"
                        class="rounded-full px-4 py-2 text-sm font-semibold"
                    >
                        {{ booking.status.charAt(0).toUpperCase() + booking.status.slice(1) }}
                    </span>
                </div>

                <!-- Property Info -->
                <div class="mb-6 flex gap-4">
                    <img 
                        :src="primaryImage"
                        :alt="booking.property.title"
                        class="h-32 w-32 rounded-lg object-cover"
                    />
                    <div>
                        <h2 class="text-xl font-semibold text-gray-900">{{ booking.property.title }}</h2>
                        <p class="mt-1 text-gray-600">{{ booking.property.city }}, {{ booking.property.state }}</p>
                        <Link
                            :href="`/properties/${booking.property.id}`"
                            class="mt-2 inline-block text-primary-600 hover:text-primary-700"
                        >
                            View Property →
                        </Link>
                    </div>
                </div>

                <!-- Booking Details -->
                <div class="mb-6 grid gap-6 md:grid-cols-2">
                    <div class="rounded-lg bg-gray-50 p-4">
                        <h3 class="mb-3 font-semibold text-gray-900">Stay Details</h3>
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Check-in:</span>
                                <span class="font-semibold">{{ new Date(booking.check_in).toLocaleDateString() }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Check-out:</span>
                                <span class="font-semibold">{{ new Date(booking.check_out).toLocaleDateString() }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Guests:</span>
                                <span class="font-semibold">{{ booking.guests }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-lg bg-gray-50 p-4">
                        <h3 class="mb-3 font-semibold text-gray-900">Price Details</h3>
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Total Price:</span>
                                <span class="text-xl font-bold text-primary-600">
                                    ₦{{ Number(booking.total_price).toLocaleString() }}
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Payment Status:</span>
                                <span class="font-semibold">Pending</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div v-if="booking.status === 'pending'" class="flex gap-4">
                    <Link
                        :href="`/payments/initialize/${booking.id}`"
                        method="post"
                        as="button"
                        class="rounded-lg bg-green-600 px-6 py-2 font-semibold text-white transition-colors hover:bg-green-700"
                    >
                        Pay Now
                    </Link>
                    <button
                        @click="cancelBooking"
                        :disabled="cancelForm.processing"
                        class="rounded-lg border-2 border-red-600 px-6 py-2 font-semibold text-red-600 transition-colors hover:bg-red-50 disabled:opacity-50"
                    >
                        {{ cancelForm.processing ? 'Cancelling...' : 'Cancel Booking' }}
                    </button>
                </div>

                <div v-else-if="booking.status === 'confirmed'" class="flex gap-4">
                    <button
                        @click="cancelBooking"
                        :disabled="cancelForm.processing"
                        class="rounded-lg border-2 border-red-600 px-6 py-2 font-semibold text-red-600 transition-colors hover:bg-red-50 disabled:opacity-50"
                    >
                        {{ cancelForm.processing ? 'Cancelling...' : 'Cancel Booking' }}
                    </button>
                </div>

                <div v-else-if="booking.status === 'cancelled'" class="rounded-lg bg-red-50 p-4 text-red-800">
                    This booking has been cancelled.
                </div>
            </div>
        </div>
    </AppLayout>
</template>
