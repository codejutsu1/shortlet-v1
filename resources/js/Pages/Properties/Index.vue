<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import PropertyCard from '@/Components/PropertyCard.vue';
import { router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    properties: Object,
    cities: Array,
    filters: Object,
});

const selectedCity = ref(props.filters.city || '');
const minPrice = ref(props.filters.min_price || '');
const maxPrice = ref(props.filters.max_price || '');

const applyFilters = () => {
    router.get('/properties', {
        city: selectedCity.value,
        min_price: minPrice.value,
        max_price: maxPrice.value,
    }, {
        preserveState: true,
    });
};

const clearFilters = () => {
    selectedCity.value = '';
    minPrice.value = '';
    maxPrice.value = '';
    router.get('/properties');
};
</script>

<template>
    <AppLayout>
        <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Explore Properties</h1>
                <p class="mt-2 text-gray-600">Find your perfect short-term rental</p>
            </div>

            <div class="flex flex-col gap-8 lg:flex-row">
                <!-- Filters Sidebar -->
                <aside class="w-full lg:w-64 shrink-0">
                    <div class="sticky top-4 rounded-xl bg-white p-6 shadow-md">
                        <h2 class="mb-4 text-lg font-semibold text-gray-900">Filters</h2>

                        <!-- City Filter -->
                        <div class="mb-4">
                            <label class="mb-2 block text-sm font-medium text-gray-700">City</label>
                            <select 
                                v-model="selectedCity"
                                class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500"
                            >
                                <option value="">All Cities</option>
                                <option v-for="city in cities" :key="city" :value="city">
                                    {{ city }}
                                </option>
                            </select>
                        </div>

                        <!-- Price Range -->
                        <div class="mb-4">
                            <label class="mb-2 block text-sm font-medium text-gray-700">Price Range (₦)</label>
                            <div class="flex gap-2">
                                <input
                                    v-model="minPrice"
                                    type="number"
                                    placeholder="Min"
                                    class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500"
                                />
                                <input
                                    v-model="maxPrice"
                                    type="number"
                                    placeholder="Max"
                                    class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500"
                                />
                            </div>
                        </div>

                        <!-- Filter Buttons -->
                        <div class="space-y-2">
                            <button
                                @click="applyFilters"
                                class="w-full rounded-lg bg-primary-600 px-4 py-2 font-semibold text-white transition-colors hover:bg-primary-700"
                            >
                                Apply Filters
                            </button>
                            <button
                                @click="clearFilters"
                                class="w-full rounded-lg border border-gray-300 px-4py-2 font-semibold text-gray-700 transition-colors hover:bg-gray-50"
                            >
                                Clear All
                            </button>
                        </div>
                    </div>
                </aside>

                <!-- Properties Grid -->
                <div class="flex-1">
                    <div v-if="properties.data.length > 0" class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                        <PropertyCard 
                            v-for="property in properties.data" 
                            :key="property.id"
                            :property="property"
                        />
                    </div>

                    <!-- Empty State -->
                    <div v-else class="rounded-xl bg-white p-12 text-center shadow-md">
                        <p class="text-lg text-gray-600">No properties found matching your criteria.</p>
                        <button
                            @click="clearFilters"
                            class="mt-4 text-primary-600 hover:text-primary-700"
                        >
                            Clear filters
                        </button>
                    </div>

                    <!-- Pagination (basic) -->
                    <div v-if="properties.data.length > 0" class="mt-8 flex justify-center gap-2">
                        <a
                            v-if="properties.prev_page_url"
                            :href="properties.prev_page_url"
                            class="rounded-lg border border-gray-300 px-4 py-2 hover:bg-gray-50"
                        >
                            Previous
                        </a>
                        <span class="rounded-lg border border-gray-300 px-4 py-2">
                            Page {{ properties.current_page }}
                        </span>
                        <a
                            v-if="properties.next_page_url"
                            :href="properties.next_page_url"
                            class="rounded-lg border border-gray-300 px-4 py-2 hover:bg-gray-50"
                        >
                            Next
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
