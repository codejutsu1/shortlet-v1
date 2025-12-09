<script setup>
import { computed } from 'vue';

const props = defineProps({
    rating: {
        type: Number,
        required: true,
        validator: (value) => value >= 0 && value <= 5
    },
    size: {
        type: String,
        default: 'medium',
        validator: (value) => ['small', 'medium', 'large'].includes(value)
    },
    showCount: {
        type: Boolean,
        default: false
    },
    reviewCount: {
        type: Number,
        default: 0
    }
});

const sizeClasses = computed(() => {
    const sizes = {
        small: 'h-4 w-4',
        medium: 'h-5 w-5',
        large: 'h-6 w-6'
    };
    return sizes[props.size];
});

const fullStars = computed(() => Math.floor(props.rating));
const hasHalfStar = computed(() => props.rating % 1 >= 0.5);
const emptyStars = computed(() => 5 - fullStars.value - (hasHalfStar.value ? 1 : 0));
</script>

<template>
    <div class="flex items-center gap-1">
        <!-- Full Stars -->
        <template v-for="i in fullStars" :key="'full-' + i">
            <svg :class="['text-yellow-400', sizeClasses]" fill="currentColor" viewBox="0 0 20 20">
                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
            </svg>
        </template>

        <!-- Half Star -->
        <template v-if="hasHalfStar">
            <svg :class="['text-yellow-400', sizeClasses]" fill="currentColor" viewBox="0 0 20 20">
                <defs>
                    <linearGradient id="half-star-gradient">
                        <stop offset="50%" stop-color="currentColor" />
                        <stop offset="50%" stop-color="#D1D5DB" />
                    </linearGradient>
                </defs>
                <path fill="url(#half-star-gradient)" d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
            </svg>
        </template>

        <!-- Empty Stars -->
        <template v-for="i in emptyStars" :key="'empty-' + i">
            <svg :class="['text-gray-300', sizeClasses]" fill="currentColor" viewBox="0 0 20 20">
                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
            </svg>
        </template>

        <!-- Review Count -->
        <span v-if="showCount && reviewCount > 0" class="ml-1 text-sm text-gray-600">
            ({{ reviewCount }})
        </span>
    </div>
</template>
