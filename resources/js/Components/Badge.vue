<script setup>
import { computed } from 'vue';

const props = defineProps({
    variant: {
        type: String,
        default: 'info',
        validator: (value) => ['success', 'warning', 'error', 'info'].includes(value),
    },
});

const badgeClasses = computed(() => {
    const base = 'inline-flex items-center px-3 py-1 text-sm font-medium rounded-full';

    const variants = {
        success: 'bg-green-100 text-green-800',
        warning: 'bg-yellow-100 text-yellow-800',
        error: 'bg-red-100 text-red-800',
        info: 'bg-blue-100 text-blue-800',
    };

    return `${base} ${variants[props.variant]}`;
});
</script>

<template>
    <span :class="badgeClasses">
        <slot />
    </span>
</template>
