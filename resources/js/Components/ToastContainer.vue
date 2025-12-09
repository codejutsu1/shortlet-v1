<script setup>
import { ref } from 'vue';
import Toast from '@/Components/Toast.vue';

const toasts = ref([]);
let idCounter = 0;

const addToast = ({ type = 'info', message, duration = 5000 }) => {
    const id = idCounter++;
    toasts.value.push({ id, type, message, duration });
};

const removeToast = (id) => {
    const index = toasts.value.findIndex(t => t.id === id);
    if (index > -1) {
        toasts.value.splice(index, 1);
    }
};

// Expose methods for external access
defineExpose({
    addToast
});
</script>

<template>
    <Teleport to="body">
        <div class="pointer-events-none fixed right-4 top-4 z-50 flex flex-col items-end">
            <Toast
                v-for="toast in toasts"
                :key="toast.id"
                :type="toast.type"
                :message="toast.message"
                :duration="toast.duration"
                class="pointer-events-auto"
                @dismiss="removeToast(toast.id)"
            />
        </div>
    </Teleport>
</template>
