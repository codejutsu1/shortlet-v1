<script setup>
import { ref, computed } from 'vue';
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
    property: {
        type: Object,
        required: true,
    },
});

const form = useForm({
    property_id: props.property.id,
    check_in: '',
    check_out: '',
    guests: 1,
});

const nights = computed(() => {
    if (!form.check_in || !form.check_out) return 0;
    const checkIn = new Date(form.check_in);
    const checkOut = new Date(form.check_out);
    const diff = checkOut - checkIn;
    return Math.max(0, Math.ceil(diff / (1000 * 60 * 60 * 24)));
});

const totalPrice = computed(() => {
    return nights.value * props.property.price_per_night;
});

const submitBooking = () => {
    form.post(route('bookings.store'), {
        onSuccess: () => {
            // Redirect handled by controller
        },
    });
};
</script>

<template>
    <div class="rounded-xl bg-white p-6 shadow-lg">
        <div class="mb-4 text-center">
            <span class="text-3xl font-bold text-gray-900">
                ₦{{ Number(property.price_per_night).toLocaleString() }}
            </span>
            <span class="text-gray-600">/night</span>
        </div>

        <form @submit.prevent="submitBooking" class="space-y-4">
            <!-- Check-in Date -->
            <div>
                <label class="mb-2 block text-sm font-medium text-gray-700">Check-in</label>
                <input
                    v-model="form.check_in"
                    type="date"
                    :min="new Date().toISOString().split('T')[0]"
                    required
                    class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500"
                />
                <div v-if="form.errors.check_in" class="mt-1 text-sm text-red-600">
                    {{ form.errors.check_in }}
                </div>
            </div>

            <!-- Check-out Date -->
            <div>
                <label class="mb-2 block text-sm font-medium text-gray-700">Check-out</label>
                <input
                    v-model="form.check_out"
                    type="date"
                    :min="form.check_in || new Date().toISOString().split('T')[0]"
                    required
                    class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500"
                />
                <div v-if="form.errors.check_out" class="mt-1 text-sm text-red-600">
                    {{ form.errors.check_out }}
                </div>
            </div>

            <!-- Guests -->
            <div>
                <label class="mb-2 block text-sm font-medium text-gray-700">Guests</label>
                <input
                    v-model.number="form.guests"
                    type="number"
                    min="1"
                    :max="property.max_guests"
                    required
                    class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500"
                />
                <p class="mt-1 text-xs text-gray-500">Maximum: {{ property.max_guests }} guests</p>
                <div v-if="form.errors.guests" class="mt-1 text-sm text-red-600">
                    {{ form.errors.guests }}
                </div>
            </div>

            <!-- Price Breakdown -->
            <div v-if="nights > 0" class="rounded-lg bg-gray-50 p-4">
                <div class="flex justify-between mb-2">
                    <span class="text-gray-700">
                        ₦{{ Number(property.price_per_night).toLocaleString() }} × {{ nights }} nights
                    </span>
                    <span class="font-semibold">₦{{ Number(totalPrice).toLocaleString() }}</span>
                </div>
                <div class="border-t border-gray-200 mt-2 pt-2 flex justify-between font-bold">
                    <span>Total</span>
                    <span>₦{{ Number(totalPrice).toLocaleString() }}</span>
                </div>
            </div>

            <!-- Error Messages -->
            <div v-if="form.errors.dates" class="rounded-lg bg-red-50 p-3 text-sm text-red-600">
                {{ form.errors.dates }}
            </div>

            <!-- Submit Button -->
            <button
                type="submit"
                :disabled="form.processing || nights === 0"
                class="w-full rounded-lg bg-primary-600 px-6 py-3 font-semibold text-white transition-colors hover:bg-primary-700 disabled:bg-gray-300 disabled:cursor-not-allowed"
            >
                {{ form.processing ? 'Processing...' : 'Reserve' }}
            </button>

            <p class="text-center text-xs text-gray-500">
                You won't be charged yet
            </p>
        </form>
    </div>
</template>
