<script setup>
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    property: {
        type: Object,
        required: true,
    },
});

const primaryImage = props.property.images?.find(img => img.is_primary)?.image_path 
    || props.property.images?.[0]?.image_path 
    || 'https://images.unsplash.com/photo-1580587771525-78b9dba3b914?w=800';
</script>

<template>
    <Link 
        :href="`/properties/${property.id}`"
        class="group block overflow-hidden rounded-xl bg-white shadow-md transition-all duration-300 hover:shadow-xl"
    >
        <!-- Property Image -->
        <div class="relative aspect-[4/3] overflow-hidden">
            <img 
                :src="primaryImage" 
                :alt="property.title"
                class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-110"
            />
            <div class="absolute right-3 top-3 rounded-lg bg-white px-3 py-1 text-sm font-bold text-primary-600">
                ₦{{ Number(property.price_per_night).toLocaleString() }}/night
            </div>
        </div>

        <!-- Property Info -->
        <div class="p-4">
            <h3 class="mb-2 text-lg font-bold text-gray-900 line-clamp-1">
                {{ property.title }}
            </h3>
            <p class="mb-3 text-sm text-gray-600">
                {{ property.city }}, {{ property.state }}
            </p>

            <!-- Property Stats -->
            <div class="flex items-center gap-4 text-sm text-gray-600">
                <span>{{ property.bedrooms }} beds</span>
                <span>•</span>
                <span>{{ property.bathrooms }} baths</span>
                <span>•</span>
                <span>{{ property.max_guests }} guests</span>
            </div>
        </div>
    </Link>
</template>
