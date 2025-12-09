<script setup>
import { computed, onMounted, ref } from 'vue';

const props = defineProps({
    type: {
        type: String,
        default: 'info',
        validator: (value) => ['success', 'error', 'warning', 'info'].includes(value)
    },
    message: {
        type: String,
        required: true
    },
    duration: {
        type: Number,
        default: 5000
    },
    dismissible: {
        type: Boolean,
        default: true
    }
});

const emit = defineEmits(['dismiss']);

const progress = ref(100);
const isVisible = ref(false);

const typeConfig = computed(() => {
    const configs = {
        success: {
            bg: 'bg-secondary-50',
            border: 'border-secondary-500',
            text: 'text-secondary-800',
            icon: 'text-secondary-600',
            iconPath: 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'
        },
        error: {
            bg: 'bg-red-50',
            border: 'border-red-500',
            text: 'text-red-800',
            icon: 'text-red-600',
            iconPath: 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z'
        },
        warning: {
            bg: 'bg-warning-light',
            border: 'border-warning',
            text: 'text-warning-dark',
            icon: 'text-warning',
            iconPath: 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z'
        },
        info: {
            bg: 'bg-primary-50',
            border: 'border-primary-500',
            text: 'text-primary-800',
            icon: 'text-primary-600',
            iconPath: 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'
        }
    };
    return configs[props.type];
});

const progressColor = computed(() => {
    const colors = {
        success: 'bg-secondary-600',
        error: 'bg-red-600',
        warning: 'bg-warning',
        info: 'bg-primary-600'
    };
    return colors[props.type];
});

let interval;
let timeout;

onMounted(() => {
    isVisible.value = true;
    
    if (props.duration > 0) {
        const step = 100 / (props.duration / 50);
        
        interval = setInterval(() => {
            progress.value -= step;
            if (progress.value <= 0) {
                clearInterval(interval);
            }
        }, 50);

        timeout = setTimeout(() => {
            dismiss();
        }, props.duration);
    }
});

const dismiss = () => {
    isVisible.value = false;
    if (interval) clearInterval(interval);
    if (timeout) clearTimeout(timeout);
    
    setTimeout(() => {
        emit('dismiss');
    }, 300);
};
</script>

<template>
    <div
        :class="[
            'transform transition-all duration-300 ease-out',
            isVisible ? 'translate-x-0 opacity-100' : 'translate-x-full opacity-0'
        ]"
    >
        <div
            :class="[
                'relative overflow-hidden rounded-lg shadow-lg',
                'border-l-4 p-4 mb-3',
                'min-w-[320px] max-w-md',
                typeConfig.bg,
                typeConfig.border
            ]"
        >
            <div class="flex items-start gap-3">
                <!-- Icon -->
                <svg
                    :class="['h-6 w-6 flex-shrink-0', typeConfig.icon]"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        :d="typeConfig.iconPath"
                    />
                </svg>

                <!-- Message -->
                <p :class="['flex-1 text-sm font-medium', typeConfig.text]">
                    {{ message }}
                </p>

                <!-- Dismiss Button -->
                <button
                    v-if="dismissible"
                    @click="dismiss"
                    :class="['flex-shrink-0 rounded-md hover:opacity-75 transition-opacity', typeConfig.icon]"
                >
                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                        <path
                            fill-rule="evenodd"
                            d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                            clip-rule="evenodd"
                        />
                    </svg>
                </button>
            </div>

            <!-- Progress Bar -->
            <div
                v-if="duration > 0"
                class="absolute bottom-0 left-0 right-0 h-1 overflow-hidden"
            >
                <div
                    :class="['h-full transition-all duration-50', progressColor]"
                    :style="{ width: `${progress}%` }"
                ></div>
            </div>
        </div>
    </div>
</template>
