<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';

defineProps({
    booking: Object,
});

const primaryImage = booking.property.images?.find(img => img.is_primary)?.image_path 
    || booking.property.images?.[0]?.image_path 
    || 'https://images.unsplash.com/photo-1580587771525-78b9dba3b914?w=800';
</script>

<template>
    <AppLayout>
        <div class="mx-auto max-w-2xl px-4 py-12 sm:px-6 lg:px-8">
            <!-- Success Icon -->
            <div class="mb-8 text-center">
                <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-green-100">
                    <svg class="h-12 w-12 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <h1 class="mt-6 text-3xl font-bold text-gray-900">Payment Successful!</h1>
                <p class="mt-2 text-gray-600">Your booking has been confirmed</p>
            </div>

            <!-- Booking Summary -->
            <div class="rounded-xl bg-white p-8 shadow-lg">
                <h2 class="mb-4 text-xl font-semibold">Booking Confirmation</h2>
                
                <div class="mb-6 flex gap-4">
                    <img 
                        :src="primaryImage"
                        :alt="booking.property.title"
                        class="h-24 w-24 rounded-lg object-cover"
                    />
                    <div>
                        <h3 class="font-semibold text-gray-900">{{ booking.property.title }}</h3>
                        <p class="text-sm text-gray-600">{{ booking.property.city }}, {{ booking.property.state }}</p>
                    </div>
                </div>

                <div class="space-y-3 border-t border-gray-200 pt-4">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Booking ID:</span>
                        <span class="font-semibold">#{{ booking.id }}</span>
                    </div>
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
                    <div class="flex justify-between border-t border-gray-200 pt-3">
                        <span class="text-lg font-semibold">Total Paid:</span>
                        <span class="text-lg font-bold text-green-600">
                            ₦{{ Number(booking.total_price).toLocaleString() }}
                        </span>
                    </div>
                </div>

                <div class="mt-6 flex gap-3">
                    <Link
                        :href="`/bookings/${booking.id}`"
                        class="flex-1 rounded-lg bg-primary-600 px-6 py-3 text-center font-semibold text-white transition-colors hover:bg-primary-700"
                    >
                        View Booking
                    </Link>
                    <Link
                        href="/properties"
                        class="flex-1 rounded-lg border border-gray-300 px-6 py-3 text-center font-semibold text-gray-700 transition-colors hover:bg-gray-50"
                    >
                        Browse More
                    </Link>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
