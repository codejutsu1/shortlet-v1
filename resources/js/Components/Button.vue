<script setup>
import { computed } from 'vue';

const props = defineProps({
    variant: {
        type: String,
        default: 'primary',
        validator: (value) => ['primary', 'secondary', 'danger', 'ghost'].includes(value),
    },
    type: {
        type: String,
        default: 'button',
    },
    disabled: {
        type: Boolean,
        default: false,
    },
});

const buttonClasses = computed(() => {
    const base =
        'px-6 py-3 font-semibold rounded-lg transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2';

    const variants = {
        primary:
            'bg-primary-600 hover:bg-primary-700 text-white shadow-md hover:shadow-lg focus:ring-primary-500',
        secondary:
            'bg-white hover:bg-gray-50 text-primary-600 border-2 border-primary-600 focus:ring-primary-500',
        danger: 'bg-red-600 hover:bg-red-700 text-white shadow-md hover:shadow-lg focus:ring-red-500',
        ghost: 'bg-transparent hover:bg-gray-100 text-gray-700',
    };

    const disabledClass = props.disabled ? 'opacity-50 cursor-not-allowed' : '';

    return `${base} ${variants[props.variant]} ${disabledClass}`;
});
</script>

<template>
    <button :type="type" :class="buttonClasses" :disabled="disabled">
        <slot />
    </button>
</template>
