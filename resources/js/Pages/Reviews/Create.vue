<script setup>
import { ref, computed } from 'vue';
import { useForm, Link } from '@inertiajs/vue3';

const props = defineProps({
    booking: {
        type: Object,
        required: true,
    },
});

const form = useForm({
    rating: 0,
    comment: '',
});

const hoveredStar = ref(0);

const setRating = (rating) => {
    form.rating = rating;
};

const submitReview = () => {
    form.post(window.route('reviews.store', props.booking.id), {
        onSuccess: () => {
            // Will redirect to property page with success message
        },
    });
};

const characterCount = computed(() => form.comment.length);
const characterLimit = 1000;
const minCharacters = 10;

const isValid = computed(() => {
    return form.rating > 0 && 
           form.comment.length >= minCharacters && 
           form.comment.length <= characterLimit;
});

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric',
    });
};

const primaryImage = computed(() => {
    return props.booking.property.images?.find(img => img.is_primary)?.image_path
        || props.booking.property.images?.[0]?.image_path
        || 'https://images.unsplash.com/photo-1580587771525-78b9dba3b914?w=400';
});
</script>

<template>
    <div class="min-h-screen bg-gray-50 py-8">
        <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Review Your Stay</h1>
                <p class="mt-2 text-gray-600">Share your experience with other travelers</p>
            </div>

            <!-- Property Card -->
            <div class="mb-8 overflow-hidden rounded-xl bg-white shadow-md">
                <div class="flex gap-4 p-4">
                    <img 
                        :src="primaryImage"
                        :alt="booking.property.title"
                        class="h-24 w-24 flex-shrink-0 rounded-lg object-cover"
                    />
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-gray-900">{{ booking.property.title }}</h3>
                        <p class="text-sm text-gray-600">{{ booking.property.city }}, {{ booking.property.state }}</p>
                        <p class="mt-1 text-sm text-gray-500">
                            {{ formatDate(booking.check_in) }} - {{ formatDate(booking.check_out) }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Review Form -->
            <form @submit.prevent="submitReview" class="rounded-xl bg-white p-6 shadow-md">
                <!-- Star Rating -->
                <div class="mb-6">
                    <label class="mb-3 block text-lg font-semibold text-gray-900">
                        How was your stay?
                    </label>
                    <div class="flex items-center gap-2">
                        <template v-for="star in 5" :key="star">
                            <svg
                                @click="setRating(star)"
                                @mouseenter="hoveredStar = star"
                                @mouseleave="hoveredStar = 0"
                                :class="[
                                    'h-12 w-12 cursor-pointer transition-all duration-150',
                                    (hoveredStar >= star || form.rating >= star)
                                        ? 'scale-110 text-yellow-400'
                                        : 'text-gray-300 hover:text-yellow-200'
                                ]"
                                fill="currentColor"
                                viewBox="0 0 20 20"
                            >
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                            </svg>
                        </template>
                    </div>
                    <p v-if="form.rating > 0" class="mt-2 text-sm text-gray-600">
                        {{ ['', 'Poor', 'Fair', 'Good', 'Very Good', 'Excellent'][form.rating] }}
                    </p>
                    <p v-if="form.errors.rating" class="mt-2 text-sm text-red-600">{{ form.errors.rating }}</p>
                </div>

                <!-- Review Text -->
                <div class="mb-6">
                    <label for="comment" class="mb-2 block text-lg font-semibold text-gray-900">
                        Tell us about your experience
                    </label>
                    <textarea
                        id="comment"
                        v-model="form.comment"
                        rows="6"
                        :class="[
                            'block w-full rounded-lg border p-3 transition-colors',
                            form.errors.comment 
                                ? 'border-red-300 focus:border-red-500 focus:ring-red-500' 
                                : 'border-gray-300 focus:border-primary-500 focus:ring-primary-500'
                        ]"
                        placeholder="Share details about your stay, the property, amenities, and host..."
                    ></textarea>
                    <div class="mt-2 flex items-center justify-between text-sm">
                        <span :class="characterCount < minCharacters ? 'text-red-600' : 'text-gray-500'">
                            Minimum {{ minCharacters }} characters
                        </span>
                        <span :class="characterCount > characterLimit ? 'text-red-600' : 'text-gray-500'">
                            {{ characterCount }} / {{ characterLimit }}
                        </span>
                    </div>
                    <p v-if="form.errors.comment" class="mt-2 text-sm text-red-600">{{ form.errors.comment }}</p>
                </div>

                <!-- Error Message -->
                <div v-if="form.errors.message" class="mb-6 rounded-lg bg-red-50 p-4">
                    <p class="text-sm text-red-600">{{ form.errors.message }}</p>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-end gap-4">
                    <Link
                        :href="window.route('bookings.index')"
                        class="rounded-lg px-6 py-3 font-semibold text-gray-700 transition-colors hover:bg-gray-100"
                    >
                        Cancel
                    </Link>
                    <button
                        type="submit"
                        :disabled="!isValid || form.processing"
                        :class="[
                            'rounded-lg px-6 py-3 font-semibold text-white transition-colors',
                            !isValid || form.processing
                                ? 'cursor-not-allowed bg-gray-400'
                                : 'bg-primary-600 hover:bg-primary-700'
                        ]"
                    >
                        <span v-if="form.processing">Submitting...</span>
                        <span v-else>Submit Review</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>
