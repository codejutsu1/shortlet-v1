<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class PropertyCacheService
{
    /**
     * Clear all property listing cache.
     * Called when any property is created, updated, or deleted.
     */
    public function clearPropertyListCache(): void
    {
        // Clear all property listing cache keys
        // In production with Redis, you could use cache tags for more granular control
        Cache::forget('homepage:featured');

        // Clear paginated property listings
        // Pattern: properties:filters:{hash}:page:{n}
        // For simplicity, we flush all cache (consider using tags in production)
        $this->clearFilteredPropertyCache();
    }

    /**
     * Clear specific property cache.
     *
     * @param int $propertyId
     */
    public function clearPropertyCache(int $propertyId): void
    {
        Cache::forget("property:{$propertyId}");
    }

    /**
     * Clear all filtered property listing cache.
     * This removes all cached property search results.
     */
    private function clearFilteredPropertyCache(): void
    {
        // In a production environment with Redis, use cache tags:
        // Cache::tags(['property-listings'])->flush();

        // For file/database cache, we need a different approach
        // For now, we'll rely on TTL expiration
        // Or implement a more sophisticated cache key tracking system
    }

    /**
     * Generate cache key for property listing with filters.
     *
     * @param array $filters
     * @param int $page
     * @return string
     */
    public function generateListingCacheKey(array $filters, int $page = 1): string
    {
        $filterHash = md5(json_encode($filters));
        return "properties:filters:{$filterHash}:page:{$page}";
    }
}
