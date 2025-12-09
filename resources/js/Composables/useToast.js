import { ref } from 'vue';

const toastContainer = ref(null);

export function useToast() {
    const showToast = ({ type = 'info', message, duration = 5000 }) => {
        if (toastContainer.value) {
            toastContainer.value.addToast({ type, message, duration });
        } else {
            console.warn('ToastContainer not found. Make sure it is added to your layout.');
        }
    };

    const success = (message, duration) => {
        showToast({ type: 'success', message, duration });
    };

    const error = (message, duration) => {
        showToast({ type: 'error', message, duration });
    };

    const warning = (message, duration) => {
        showToast({ type: 'warning', message, duration });
    };

    const info = (message, duration) => {
        showToast({ type: 'info', message, duration });
    };

    const setContainer = (container) => {
        toastContainer.value = container;
    };

    return {
        showToast,
        success,
        error,
        warning,
        info,
        setContainer
    };
}
