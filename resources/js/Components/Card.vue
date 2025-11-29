<script setup>
defineProps({
    padding: {
        type: String,
        default: 'p-6',
    },
    shadow: {
        type: String,
        default: 'shadow-md hover:shadow-lg',
    },
});
</script>

<template>
    <div
        :class="`bg-white rounded-lg transition-shadow duration-200 overflow-hidden ${shadow} ${padding}`"
    >
        <slot />
    </div>
</template>
