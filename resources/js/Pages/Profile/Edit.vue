<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { useForm, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
    user: Object
});

// Profile form
const form = useForm({
    name: props.user.name,
    email: props.user.email,
    phone: props.user.phone || '',
});

// Password form
const passwordForm = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

// Avatar upload
const avatarInput = ref(null);
const avatarPreview = ref(null);

const avatarUrl = computed(() => {
    if (avatarPreview.value) return avatarPreview.value;
    return props.user.avatar 
        ? `/storage/${props.user.avatar}`
        : `https://ui-avatars.com/api/?name=${encodeURIComponent(props.user.name)}&background=6366f1&color=fff&size=200`;
});

const updateProfile = () => {
    form.put(route('profile.update'), {
        preserveScroll: true,
    });
};

const updatePassword = () => {
    passwordForm.put(route('profile.password'), {
        preserveScroll: true,
        onSuccess: () => passwordForm.reset(),
    });
};

const selectAvatar = () => {
    avatarInput.value.click();
};

const updateAvatar = (event) => {
    const file = event.target.files[0];
    if (file) {
        // Show preview
        const reader = new FileReader();
        reader.onload = (e) => {
            avatarPreview.value = e.target.result;
        };
        reader.readAsDataURL(file);

        // Upload
        const formData = new FormData();
        formData.append('avatar', file);
        
        router.post(route('profile.avatar'), formData, {
            preserveScroll: true,
            onSuccess: () => {
                avatarPreview.value = null;
            },
        });
    }
};

const deleteAvatar = () => {
    if (confirm('Are you sure you want to remove your profile picture?')) {
        router.delete(route('profile.avatar.delete'), {
            preserveScroll: true,
        });
    }
};
</script>

<template>
    <AppLayout>
        <div class="mx-auto max-w-3xl px-4 py-8 sm:px-6 lg:px-8">
            <h1 class="mb-8 text-3xl font-bold text-gray-900">Edit Profile</h1>

            <div class="space-y-6">
                <!-- Profile Information -->
                <div class="rounded-xl bg-white p-6 shadow-md">
                    <h2 class="mb-4 text-xl font-semibold text-gray-900">Profile Information</h2>
                    
                    <form @submit.prevent="updateProfile" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Name</label>
                            <input 
                                v-model="form.name"
                                type="text"
                                class="mt-1 w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-primary-500 focus:ring-2 focus:ring-primary-500"
                                required
                            />
                            <span v-if="form.errors.name" class="mt-1 text-sm text-red-600">
                                {{ form.errors.name }}
                            </span>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Email</label>
                            <input 
                                v-model="form.email"
                                type="email"
                                class="mt-1 w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-primary-500 focus:ring-2 focus:ring-primary-500"
                                required
                            />
                            <span v-if="form.errors.email" class="mt-1 text-sm text-red-600">
                                {{ form.errors.email }}
                            </span>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Phone</label>
                            <input 
                                v-model="form.phone"
                                type="tel"
                                class="mt-1 w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-primary-500 focus:ring-2 focus:ring-primary-500"
                                placeholder="+234 XXX XXX XXXX"
                            />
                            <span v-if="form.errors.phone" class="mt-1 text-sm text-red-600">
                                {{ form.errors.phone }}
                            </span>
                        </div>

                        <div class="flex gap-3">
                            <button 
                                type="submit"
                                :disabled="form.processing"
                                class="rounded-lg bg-primary-600 px-6 py-2 font-semibold text-white transition-colors hover:bg-primary-700 disabled:opacity-50"
                            >
                                {{ form.processing ? 'Saving...' : 'Save Changes' }}
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Avatar Upload -->
                <div class="rounded-xl bg-white p-6 shadow-md">
                    <h2 class="mb-4 text-xl font-semibold text-gray-900">Profile Picture</h2>
                    
                    <div class="flex items-center gap-6">
                        <img 
                            :src="avatarUrl"
                            :alt="user.name"
                            class="h-24 w-24 rounded-full object-cover ring-4 ring-gray-100"
                        />
                        
                        <div class="flex-1">
                            <input 
                                ref="avatarInput"
                                type="file"
                                accept="image/*"
                                @change="updateAvatar"
                                class="hidden"
                            />
                            <div class="flex gap-3">
                                <button 
                                    @click="selectAvatar"
                                    class="rounded-lg border-2 border-primary-600 px-6 py-2 font-semibold text-primary-600 transition-colors hover:bg-primary-50"
                                >
                                    Change Avatar
                                </button>
                                <button 
                                    v-if="user.avatar"
                                    @click="deleteAvatar"
                                    class="rounded-lg border-2 border-red-600 px-6 py-2 font-semibold text-red-600 transition-colors hover:bg-red-50"
                                >
                                    Remove
                                </button>
                            </div>
                            <p class="mt-2 text-sm text-gray-500">
                                JPG, PNG, or WebP. Max 2MB.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Password Change -->
                <div class="rounded-xl bg-white p-6 shadow-md">
                    <h2 class="mb-4 text-xl font-semibold text-gray-900">Change Password</h2>
                    
                    <form @submit.prevent="updatePassword" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Current Password</label>
                            <input 
                                v-model="passwordForm.current_password"
                                type="password"
                                class="mt-1 w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-primary-500 focus:ring-2 focus:ring-primary-500"
                                required
                            />
                            <span v-if="passwordForm.errors.current_password" class="mt-1 text-sm text-red-600">
                                {{ passwordForm.errors.current_password }}
                            </span>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">New Password</label>
                            <input 
                                v-model="passwordForm.password"
                                type="password"
                                class="mt-1 w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-primary-500 focus:ring-2 focus:ring-primary-500"
                                required
                            />
                            <p class="mt-1 text-sm text-gray-500">Minimum 8 characters</p>
                            <span v-if="passwordForm.errors.password" class="mt-1 text-sm text-red-600">
                                {{ passwordForm.errors.password }}
                            </span>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Confirm New Password</label>
                            <input 
                                v-model="passwordForm.password_confirmation"
                                type="password"
                                class="mt-1 w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-primary-500 focus:ring-2 focus:ring-primary-500"
                                required
                            />
                        </div>

                        <div class="flex gap-3">
                            <button 
                                type="submit"
                                :disabled="passwordForm.processing"
                                class="rounded-lg bg-primary-600 px-6 py-2 font-semibold text-white transition-colors hover:bg-primary-700 disabled:opacity-50"
                            >
                                {{ passwordForm.processing ? 'Updating...' : 'Update Password' }}
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Back Button -->
                <div class="flex justify-center">
                    <a 
                        :href="route('profile.show')"
                        class="text-primary-600 hover:text-primary-700"
                    >
                        ← Back to Profile
                    </a>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
