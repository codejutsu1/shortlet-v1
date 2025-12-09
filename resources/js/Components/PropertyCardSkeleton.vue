<template>
    <div class="overflow-hidden rounded-lg bg-white shadow-md transition-shadow hover:shadow-lg">
        <!-- Image Skeleton -->
        <div class="h-48 w-full animate-pulse bg-gray-200"></div>
        
        <!-- Content Skeleton -->
        <div class="p-6 space-y-4">
            <!-- Title -->
            <div class="h-6 w-3/4 animate-pulse rounded bg-gray-200"></div>
            
            <!-- Location -->
            <div class="h-4 w-1/2 animate-pulse rounded bg-gray-200"></div>
            
            <!-- Stats -->
            <div class="flex gap-4">
                <div class="h-4 w-16 animate-pulse rounded bg-gray-200"></div>
                <div class="h-4 w-16 animate-pulse rounded bg-gray-200"></div>
                <div class="h-4 w-16 animate-pulse rounded bg-gray-200"></div>
            </div>
            
            <!-- Price -->
            <div class="mt-4 flex items-center justify-between">
                <div class="h-8 w-24 animate-pulse rounded bg-gray-200"></div>
                <div class="h-4 w-16 animate-pulse rounded bg-gray-200"></div>
            </div>
        </div>
    </div>
</template>

<style scoped>
@keyframes shimmer {
    0% {
        background-position: -468px 0;
    }
    100% {
        background-position: 468px 0;
    }
}

.animate-pulse {
    animation: shimmer 2s infinite;
    background: linear-gradient(to right, #f3f4f6 0%, #e5e7eb 20%, #f3f4f6 40%, #f3f4f6 100%);
    background-size: 800px 104px;
}
</style>
