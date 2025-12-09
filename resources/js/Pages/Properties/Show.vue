<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import BookingWidget from '@/Components/BookingWidget.vue';
import ReviewCard from '@/Components/ReviewCard.vue';
import RatingStatistics from '@/Components/RatingStatistics.vue';

const props = defineProps({
    property: {
        type: Object,
        required: true,
    },
    similarProperties: {
        type: Array,
        default: () => [],
    },
    averageRating: Number,
    reviewCount: Number,
    ratingBreakdown: Object,
    reviewSort: {
        type: String,
        default: 'newest'
    },
});

// Image gallery state
const showLightbox = ref(false);
const currentImageIndex = ref(0);

const allImages = computed(() => props.property.images || []);
const primaryImage = computed(() => {
    return allImages.value.find(img => img.is_primary)?.image_path
        || allImages.value[0]?.image_path
        || 'https://images.unsplash.com/photo-1580587771525-78b9dba3b914?w=1200';
});

const openLightbox = (index) => {
    currentImageIndex.value = index;
    showLightbox.value = true;
    document.body.style.overflow = 'hidden';
};

const closeLightbox = () => {
    showLightbox.value = false;
    document.body.style.overflow = '';
};

const nextImage = () => {
    if (currentImageIndex.value < allImages.value.length - 1) {
        currentImageIndex.value++;
    }
};

const prevImage = () => {
    if (currentImageIndex.value > 0) {
        currentImageIndex.value--;
    }
};

const handleKeydown = (e) => {
    if (!showLightbox.value) return;
    
    if (e.key === 'Escape') closeLightbox();
    if (e.key === 'ArrowRight') nextImage();
    if (e.key === 'ArrowLeft') prevImage();
};

onMounted(() => {
    window.addEventListener('keydown', handleKeydown);
});

onUnmounted(() => {
    window.removeEventListener('keydown', handleKeydown);
    document.body.style.overflow = '';
});

// Get primary image for similar properties
const getSimilarPropertyImage = (property) => {
    return property.images?.find(img => img.is_primary)?.image_path
        || property.images?.[0]?.image_path
        || 'https://images.unsplash.com/photo-1580587771525-78b9dba3b914?w=800';
};

// Handle review sort change
const handleSortChange = (event) => {
    router.get(window.location.pathname, {
        review_sort: event.target.value
    }, {
        preserveState: true,
        preserveScroll: true,
    });
};
</script>

<template>
    <div class="min-h-screen bg-gray-50">
        <!-- Header with back button -->
        <div class="bg-white shadow-sm">
            <div class="mx-auto max-w-7xl px-4 py-4 sm:px-6 lg:px-8">
                <Link href="/properties" class="inline-flex items-center text-primary-600 hover:text-primary-700">
                    <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Back to Properties
                </Link>
            </div>
        </div>

        <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            <!-- Image Gallery -->
            <div class="mb-8">
                <div v-if="allImages.length >= 5" class="grid grid-cols-4 gap-2">
                    <!-- Large image -->
                    <div class="col-span-2 row-span-2">
                        <img 
                            :src="allImages[0].image_path"
                            :alt="property.title"
                            @click="openLightbox(0)"
                            class="h-full w-full cursor-pointer rounded-l-xl object-cover transition-opacity hover:opacity-90"
                        />
                    </div>
                    <!-- Small images -->
                    <div v-for="(image, index) in allImages.slice(1, 5)" :key="index">
                        <img 
                            :src="image.image_path"
                            :alt="`${property.title} - Image ${index + 2}`"
                            @click="openLightbox(index + 1)"
                            :class="[
                                'h-48 w-full cursor-pointer object-cover transition-opacity hover:opacity-90',
                                index === 1 ? 'rounded-tr-xl' : '',
                                index === 3 ? 'rounded-br-xl' : ''
                            ]"
                        />
                    </div>
                </div>
                
                <!-- Fallback for < 5 images -->
                <div v-else class="grid gap-4 md:grid-cols-2">
                    <div class="md:col-span-2">
                        <img 
                            :src="primaryImage"
                            :alt="property.title"
                            @click="openLightbox(0)"
                            class="h-96 w-full cursor-pointer rounded-xl object-cover shadow-lg transition-opacity hover:opacity-90"
                        />
                    </div>
                    <template v-if="allImages.length > 1">
                        <img 
                            v-for="(image, index) in allImages.slice(1, 3)" 
                            :key="index"
                            :src="image.image_path"
                            @click="openLightbox(index + 1)"
                            class="h-48 w-full cursor-pointer rounded-xl object-cover shadow-md transition-opacity hover:opacity-90"
                        />
                    </template>
                </div>
                
                <!-- View all photos button -->
                <button
                    v-if="allImages.length > 0"
                    @click="openLightbox(0)"
                    class="mt-4 inline-flex items-center gap-2 rounded-lg border-2 border-gray-900 bg-white px-4 py-2 font-semibold text-gray-900 transition-colors hover:bg-gray-50"
                >
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    View all {{ allImages.length }} photos
                </button>
            </div>

            <div class="grid gap-8 lg:grid-cols-3">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Title & Location -->
                    <div>
                        <h1 class="mb-2 text-3xl font-bold text-gray-900">{{ property.title }}</h1>
                        <p class="text-lg text-gray-600">{{ property.city }}, {{ property.state }}</p>
                    </div>

                    <!-- Property Stats -->
                    <div class="flex flex-wrap gap-6 rounded-xl bg-white p-6 shadow-md">
                        <div>
                            <p class="text-sm text-gray-600">Bedrooms</p>
                            <p class="text-xl font-semibold">{{ property.bedrooms }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Bathrooms</p>
                            <p class="text-xl font-semibold">{{ property.bathrooms }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Max Guests</p>
                            <p class="text-xl font-semibold">{{ property.max_guests }}</p>
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="rounded-xl bg-white p-6 shadow-md">
                        <h2 class="mb-4 text-xl font-semibold">About this property</h2>
                        <p class="whitespace-pre-line text-gray-700">{{ property.description }}</p>
                    </div>

                    <!-- Amenities -->
                    <div v-if="property.amenities && property.amenities.length > 0" class="rounded-xl bg-white p-6 shadow-md">
                        <h2 class="mb-4 text-xl font-semibold">Amenities</h2>
                        <div class="grid gap-3 sm:grid-cols-2">
                            <div 
                                v-for="amenity in property.amenities" 
                                :key="amenity.id"
                                class="flex items-center"
                            >
                                <svg class="mr-2 h-5 w-5 text-primary-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                                <span class="text-gray-700">{{ amenity.name }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Reviews Section -->
                    <div v-if="reviewCount > 0" class="rounded-xl bg-white p-6 shadow-md">
                        <div class="mb-6 flex items-center justify-between">
                            <h2 class="text-xl font-semibold">Reviews</h2>
                            
                            <!-- Sort Dropdown -->
                            <select
                                :value="reviewSort"
                                @change="handleSortChange"
                                class="rounded-lg border-gray-300 text-sm focus:border-primary-500 focus:ring-primary-500"
                            >
                                <option value="newest">Newest First</option>
                                <option value="highest_rated">Highest Rated</option>
                                <option value="lowest_rated">Lowest Rated</option>
                            </select>
                        </div>
                        
                        <!-- Rating Statistics -->
                        <RatingStatistics
                            :averageRating="averageRating"
                            :reviewCount="reviewCount"
                            :ratingBreakdown="ratingBreakdown"
                            class="mb-8"
                        />

                        <!-- Review Cards -->
                        <div class="space-y-6">
                            <ReviewCard
                                v-for="review in property.reviews"
                                :key="review.id"
                                :review="review"
                            />
                        </div>
                    </div>

                    <!-- No Reviews State -->
                    <div v-else class="rounded-xl bg-white p-6 text-center shadow-md">
                        <p class="text-gray-600">No reviews yet. Be the first to review this property!</p>
                    </div>
                </div>

                <!-- Booking Card (Sticky) -->
                <div class="lg:col-span-1">
                    <div class="sticky top-4">
                        <BookingWidget v-if="$page.props.auth.user" :property="property" />
                        <div v-else class="rounded-xl bg-white p-6 shadow-lg">
                            <div class="mb-4 text-center">
                                <span class="text-3xl font-bold text-gray-900">
                                    ₦{{ Number(property.price_per_night).toLocaleString() }}
                                </span>
                                <span class="text-gray-600">/night</span>
                            </div>

                            <p class="mb-4 text-center text-sm text-gray-600">
                                Sign in to book this property
                            </p>

                            <Link
                                href="/login"
                                class="block w-full rounded-lg bg-primary-600 px-6 py-3 text-center font-semibold text-white transition-colors hover:bg-primary-700"
                            >
                                Login to Book
                            </Link>

                            <p class="mt-4 text-center text-xs text-gray-500">
                                You won't be charged yet
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Similar Properties -->
            <div v-if="similarProperties.length > 0" class="mt-12">
                <h2 class="mb-6 text-2xl font-bold text-gray-900">Similar Properties</h2>
                <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                    <Link 
                        v-for="similar in similarProperties" 
                        :key="similar.id"
                        :href="`/properties/${similar.id}`"
                        class="group overflow-hidden rounded-xl bg-white shadow-md transition-shadow hover:shadow-xl"
                    >
                        <div class="aspect-[4/3]">
                            <img 
                                :src="getSimilarPropertyImage(similar)"
                                :alt="similar.title"
                                class="h-full w-full object-cover transition-transform group-hover:scale-110"
                            />
                        </div>
                        <div class="p-4">
                            <h3 class="mb-1 font-semibold text-gray-900 line-clamp-1">{{ similar.title }}</h3>
                            <p class="mb-2 text-sm text-gray-600">{{ similar.city }}</p>
                            <p class="font-bold text-primary-600">₦{{ Number(similar.price_per_night).toLocaleString() }}/night</p>
                        </div>
                    </Link>
                </div>
            </div>
        </div>

        <!-- Lightbox Modal -->
        <Teleport to="body">
            <div 
                v-if="showLightbox"
                class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-95"
                @click="closeLightbox"
            >
                <!-- Close Button -->
                <button
                    @click="closeLightbox"
                    class="absolute right-4 top-4 z-10 rounded-full bg-white p-2 text-gray-900 transition-colors hover:bg-gray-200"
                >
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

                <!-- Image Counter -->
                <div class="absolute right-4 top-16 rounded-lg bg-black bg-opacity-50 px-3 py-1 text-white">
                    {{ currentImageIndex + 1 }} / {{ allImages.length }}
                </div>

                <!-- Previous Button -->
                <button
                    v-if="currentImageIndex > 0"
                    @click.stop="prevImage"
                    class="absolute left-4 rounded-full bg-white p-3 text-gray-900 transition-colors hover:bg-gray-200"
                >
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>

                <!-- Image -->
                <img 
                    :src="allImages[currentImageIndex]?.image_path"
                    :alt="`${property.title} - Image ${currentImageIndex + 1}`"
                    class="max-h-[90vh] max-w-[90vw] object-contain"
                    @click.stop
                />

                <!-- Next Button -->
                <button
                    v-if="currentImageIndex < allImages.length - 1"
                    @click.stop="nextImage"
                    class="absolute right-4 rounded-full bg-white p-3 text-gray-900 transition-colors hover:bg-gray-200"
                >
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
            </div>
        </Teleport>
    </div>
</template>
