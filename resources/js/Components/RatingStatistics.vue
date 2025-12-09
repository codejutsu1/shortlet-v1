<script setup>
import { computed } from 'vue';
import StarRating from '@/Components/StarRating.vue';

const props = defineProps({
    averageRating: {
        type: Number,
        required: true
    },
    reviewCount: {
        type: Number,
        required: true
    },
    ratingBreakdown: {
        type: Object,
        required: true
    }
});

const getRatingPercentage = (star) => {
    const count = props.ratingBreakdown[star] || 0;
    return props.reviewCount > 0 ? (count / props.reviewCount) * 100 : 0;
};

const formattedAverage = computed(() => {
    return props.averageRating.toFixed(1);
});
</script>

<template>
    <div class="space-y-6">
        <!-- Overall Rating -->
        <div class="flex items-center gap-6">
            <!-- Large Rating Number -->
            <div class="text-center">
                <div class="text-5xl font-bold text-gray-900">{{ formattedAverage }}</div>
                <StarRating :rating="averageRating" size="small" class="mt-2" />
            </div>

            <!-- Review Count -->
            <div class="border-l border-gray-300 pl-6">
                <p class="text-2xl font-semibold text-gray-900">{{ reviewCount }}</p>
                <p class="text-sm text-gray-600">
                    {{ reviewCount === 1 ? 'review' : 'reviews' }}
                </p>
            </div>
        </div>

        <!-- Rating Breakdown -->
        <div class="space-y-2">
            <div 
                v-for="star in [5, 4, 3, 2, 1]" 
                :key="star" 
                class="flex items-center gap-3 text-sm"
            >
                <!-- Star Label -->
                <span class="w-10 font-medium text-gray-700">{{ star }} ★</span>
                
                <!-- Progress Bar -->
                <div class="h-2 flex-1 rounded-full bg-gray-200">
                    <div 
                        class="h-full rounded-full bg-yellow-400 transition-all duration-300"
                        :style="{ width: `${getRatingPercentage(star)}%` }"
                    ></div>
                </div>
                
                <!-- Count -->
                <span class="w-10 text-right text-gray-600">
                    {{ ratingBreakdown[star] || 0 }}
                </span>
            </div>
        </div>
    </div>
</template>
