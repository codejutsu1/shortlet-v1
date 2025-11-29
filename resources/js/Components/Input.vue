<script setup>
import { computed } from 'vue';

const props = defineProps({
    modelValue: {
        type: [String, Number],
        default: '',
    },
    label: {
        type: String,
        default: '',
    },
    type: {
        type: String,
        default: 'text',
    },
    error: {
        type: String,
        default: '',
    },
    placeholder: {
        type: String,
        default: '',
    },
    required: {
        type: Boolean,
        default: false,
    },
});

defineEmits(['update:modelValue']);

const inputClasses = computed(() => {
    const base =
        'w-full px-4 py-3 text-gray-900 bg-white border rounded-md focus:outline-none focus:ring-2 focus:border-transparent placeholder:text-gray-400';

    return props.error
        ? `${base} border-red-300 focus:ring-red-500`
        : `${base} border-gray-300 focus:ring-primary-500`;
});
</script>

<template>
    <div class="w-full">
        <label v-if="label" class="mb-2 block text-sm font-medium text-gray-700">
            {{ label }}
            <span v-if="required" class="text-red-500">*</span>
        </label>
        <input
            :type="type"
            :value="modelValue"
            :class="inputClasses"
            :placeholder="placeholder"
            @input="$emit('update:modelValue', $event.target.value)"
        />
        <p v-if="error" class="mt-1 text-sm text-red-600">{{ error }}</p>
    </div>
</template>
