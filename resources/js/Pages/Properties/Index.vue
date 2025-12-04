<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import PropertyCard from '@/Components/PropertyCard.vue';
import { router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
    properties: Object,
    cities: Array,
    amenities: Array,
    filters: Object,
});

// Filter state
const filters = ref({
    city: props.filters.city || '',
    check_in: props.filters.check_in || '',
    check_out: props.filters.check_out || '',
    guests: props.filters.guests || 1,
    min_price: props.filters.min_price || '',
    max_price: props.filters.max_price || '',
    amenities: props.filters.amenities || [],
    sort_by: props.filters.sort_by || 'newest',
});

// Computed properties
const hasActiveFilters = computed(() => {
    return filters.value.city || filters.value.check_in || filters.value.check_out ||
           filters.value.guests > 1 || filters.value.min_price || filters.value.max_price ||
           filters.value.amenities.length > 0;
});

const applyFilters = () => {
    router.get('/properties', {
        ...filters.value,
        amenities: filters.value.amenities.length > 0 ? filters.value.amenities : null,
    }, {
        preserveState: true,
    });
};

const clearFilters = () => {
    filters.value = {
        city: '',
        check_in: '',
        check_out: '',
        guests: 1,
        min_price: '',
        max_price: '',
        amenities: [],
        sort_by: 'newest',
    };
    router.get('/properties');
};

const removeFilter = (filterName) => {
    if (filterName === 'dates') {
        filters.value.check_in = '';
        filters.value.check_out = '';
    } else if (filterName === 'price') {
        filters.value.min_price = '';
        filters.value.max_price = '';
    } else {
        filters.value[filterName] = filterName === 'guests' ? 1 : (filterName === 'amenities' ? [] : '');
    }
    applyFilters();
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
                                v-model="filters.city"
                                class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500"
                            >
                                <option value="">All Cities</option>
                                <option v-for="city in cities" :key="city" :value="city">
                                    {{ city }}
                                </option>
                            </select>
                        </div>

                        <!-- Date Range -->
                        <div class="mb-4">
                            <label class="mb-2 block text-sm font-medium text-gray-700">Check-in</label>
                            <input
                                v-model="filters.check_in"
                                type="date"
                                :min="new Date().toISOString().split('T')[0]"
                                class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500"
                            />
                        </div>

                        <div class="mb-4">
                            <label class="mb-2 block text-sm font-medium text-gray-700">Check-out</label>
                            <input
                                v-model="filters.check_out"
                                type="date"
                                :min="filters.check_in || new Date().toISOString().split('T')[0]"
                                class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500"
                            />
                        </div>

                        <!-- Guest Count -->
                        <div class="mb-4">
                            <label class="mb-2 block text-sm font-medium text-gray-700">Guests</label>
                            <input
                                v-model.number="filters.guests"
                                type="number"
                                min="1"
                                max="20"
                                class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500"
                            />
                        </div>

                        <!-- Price Range -->
                        <div class="mb-4">
                            <label class="mb-2 block text-sm font-medium text-gray-700">Price Range (₦)</label>
                            <div class="flex gap-2">
                                <input
                                    v-model="filters.min_price"
                                    type="number"
                                    placeholder="Min"
                                    class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500"
                                />
                                <input
                                    v-model="filters.max_price"
                                    type="number"
                                    placeholder="Max"
                                    class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500"
                                />
                            </div>
                        </div>

                        <!-- Amenities -->
                        <div v-if="amenities.length > 0" class="mb-4">
                            <label class="mb-2 block text-sm font-medium text-gray-700">Amenities</label>
                            <div class="space-y-2 max-h-48 overflow-y-auto">
                                <label
                                    v-for="amenity in amenities"
                                    :key="amenity.id"
                                    class="flex items-center gap-2"
                                >
                                    <input
                                        v-model="filters.amenities"
                                        type="checkbox"
                                        :value="amenity.id"
                                        class="rounded border-gray-300 text-primary-600 focus:ring-primary-500"
                                    />
                                    <span class="text-sm text-gray-700">{{ amenity.name }}</span>
                                </label>
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
                                v-if="hasActiveFilters"
                                @click="clearFilters"
                                class="w-full rounded-lg border border-gray-300 px-4 py-2 font-semibold text-gray-700 transition-colors hover:bg-gray-50"
                            >
                                Clear All
                            </button>
                        </div>
                    </div>
                </aside>

                <!-- Properties Grid -->
                <div class="flex-1">
                    <!-- Results Header -->
                    <div class="mb-6 rounded-xl bg-white p-4 shadow-md">
                        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                            <!-- Results Count -->
                            <div>
                                <p class="text-lg font-semibold text-gray-900">
                                    {{ properties.total }} {{ properties.total === 1 ? 'property' : 'properties' }} found
                                </p>
                                
                                <!-- Active Filters Badges -->
                                <div v-if="hasActiveFilters" class="mt-2 flex flex-wrap gap-2">
                                    <span v-if="filters.city" class="inline-flex items-center gap-1 rounded-full bg-primary-100 px-3 py-1 text-sm text-primary-800">
                                        {{ filters.city }}
                                        <button @click="removeFilter('city')" class="hover:text-primary-900">×</button>
                                    </span>
                                    <span v-if="filters.check_in && filters.check_out" class="inline-flex items-center gap-1 rounded-full bg-primary-100 px-3 py-1 text-sm text-primary-800">
                                        {{ new Date(filters.check_in).toLocaleDateString() }} - {{ new Date(filters.check_out).toLocaleDateString() }}
                                        <button @click="removeFilter('dates')" class="hover:text-primary-900">×</button>
                                    </span>
                                    <span v-if="filters.guests > 1" class="inline-flex items-center gap-1 rounded-full bg-primary-100 px-3 py-1 text-sm text-primary-800">
                                        {{ filters.guests }} guests
                                        <button @click="removeFilter('guests')" class="hover:text-primary-900">×</button>
                                    </span>
                                    <span v-if="filters.min_price || filters.max_price" class="inline-flex items-center gap-1 rounded-full bg-primary-100 px-3 py-1 text-sm text-primary-800">
                                        ₦{{ filters.min_price || '0' }}-{{ filters.max_price || '∞' }}
                                        <button @click="removeFilter('price')" class="hover:text-primary-900">×</button>
                                    </span>
                                    <span v-if="filters.amenities.length > 0" class="inline-flex items-center gap-1 rounded-full bg-primary-100 px-3 py-1 text-sm text-primary-800">
                                        {{ filters.amenities.length }} {{ filters.amenities.length === 1 ? 'amenity' : 'amenities' }}
                                        <button @click="removeFilter('amenities')" class="hover:text-primary-900">×</button>
                                    </span>
                                </div>
                            </div>

                            <!-- Sort Dropdown -->
                            <div class="flex items-center gap-2">
                                <label class="text-sm text-gray-600">Sort by:</label>
                                <select
                                    v-model="filters.sort_by"
                                    @change="applyFilters"
                                    class="rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500"
                                >
                                    <option value="newest">Newest</option>
                                    <option value="price_low">Price: Low to High</option>
                                    <option value="price_high">Price: High to Low</option>
                                    <option value="rating">Highest Rated</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Grid -->
                    <div v-if="properties.data.length > 0" class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                        <PropertyCard 
                            v-for="property in properties.data" 
                            :key="property.id"
                            :property="property"
                        />
                    </div>

                    <!-- Empty State -->
                    <div v-else class="rounded-xl bg-white p-12 text-center shadow-md">
                        <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        <p class="mt-4 text-lg text-gray-600">No properties found matching your criteria.</p>
                        <button
                            @click="clearFilters"
                            class="mt-4 text-primary-600 hover:text-primary-700"
                        >
                            Clear all filters
                        </button>
                    </div>

                    <!-- Pagination -->
                    <div v-if="properties.data.length > 0 && properties.last_page > 1" class="mt-8">
                        <div class="flex items-center justify-center gap-2">
                            <a
                                v-if="properties.prev_page_url"
                                :href="properties.prev_page_url"
                                class="rounded-lg border border-gray-300 px-4 py-2 hover:bg-gray-50"
                            >
                                ← Previous
                            </a>
                            
                            <span class="px-4 py-2 text-sm text-gray-700">
                                Page {{ properties.current_page }} of {{ properties.last_page }}
                            </span>
                            
                            <a
                                v-if="properties.next_page_url"
                                :href="properties.next_page_url"
                                class="rounded-lg border border-gray-300 px-4 py-2 hover:bg-gray-50"
                            >
                                Next →
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
