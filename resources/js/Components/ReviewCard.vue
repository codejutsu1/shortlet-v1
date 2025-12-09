<script setup>
import StarRating from '@/Components/StarRating.vue';

const props = defineProps({
    review: {
        type: Object,
        required: true
    },
    showVerifiedBadge: {
        type: Boolean,
        default: true
    }
});

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('en-US', {
        month: 'long',
        year: 'numeric'
    });
};

const getUserInitial = (name) => {
    return name.charAt(0).toUpperCase();
};
</script>

<template>
    <div class="border-t border-gray-200 pt-6 first:border-t-0 first:pt-0">
        <div class="mb-3 flex items-start justify-between">
            <!-- User Info -->
            <div class="flex items-center gap-3">
                <!-- Avatar -->
                <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-primary-100 text-lg font-semibold text-primary-700">
                    {{ getUserInitial(review.user.name) }}
                </div>
                
                <!-- Name & Date -->
                <div>
                    <p class="font-semibold text-gray-900">{{ review.user.name }}</p>
                    <p class="text-sm text-gray-500">{{ formatDate(review.created_at) }}</p>
                </div>
            </div>

            <!-- Rating -->
            <StarRating :rating="review.rating" size="small" />
        </div>

        <!-- Verified Badge -->
        <div v-if="showVerifiedBadge" class="mb-3 inline-flex items-center gap-1.5 rounded-full bg-secondary-100 px-3 py-1 text-sm font-medium text-secondary-700">
            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
            </svg>
            Verified Booking
        </div>

        <!-- Comment -->
        <p class="text-gray-700 leading-relaxed">{{ review.comment }}</p>
    </div>
</template>
