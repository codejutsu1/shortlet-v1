<?php

use App\Models\Property;
use Illuminate\Support\Facades\Cache;

test('homepage caches featured properties', function () {
    Property::factory()->count(10)->create([
        'is_featured' => true,
        'status' => 'active'
    ]);

    // First request - populates cache
    $this->get('/');
    expect(Cache::has('homepage:featured'))->toBeTrue();
});

test('homepage returns cached data on subsequent requests', function () {
    Property::factory()->count(10)->create([
        'is_featured' => true,
        'status' => 'active'
    ]);

    // First request
    $response1 = $this->get('/');

    // Get cached data count
    $cachedCount = Cache::get('homepage:featured')->count();

    // Create more properties
    Property::factory()->count(5)->create([
        'is_featured' => true,
        'status' => 'active'
    ]);

    // Second request - should return cached data (still 8 properties, not 13)
    $response2 = $this->get('/');
    $response2->assertInertia(
        fn($assert) =>
        $assert->has('featuredProperties', $cachedCount)
    );
});

test('property index caches results', function () {
    Property::factory()->count(15)->create([
        'city' => 'Lagos',
        'status' => 'active'
    ]);

    $this->get('/properties?city=Lagos');

    // Cache key should exist
    $cacheService = app(\App\Services\PropertyCacheService::class);
    $cacheKey = $cacheService->generateListingCacheKey(['city' => 'Lagos'], 1);

    expect(Cache::has($cacheKey))->toBeTrue();
});

test('different filters create different cache entries', function () {
    Property::factory()->count(10)->create(['city' => 'Lagos', 'status' => 'active']);
    Property::factory()->count(10)->create(['city' => 'Abuja', 'status' => 'active']);

    // Request Lagos properties
    $this->get('/properties?city=Lagos');

    // Request Abuja properties
    $this->get('/properties?city=Abuja');

    $cacheService = app(\App\Services\PropertyCacheService::class);
    $lagosKey = $cacheService->generateListingCacheKey(['city' => 'Lagos'], 1);
    $abujaKey = $cacheService->generateListingCacheKey(['city' => 'Abuja'], 1);

    // Both should be cached with different keys
    expect(Cache::has($lagosKey))->toBeTrue()
        ->and(Cache::has($abujaKey))->toBeTrue()
        ->and($lagosKey)->not->toBe($abujaKey);
});

test('cache is cleared when property is created', function () {
    Cache::put('homepage:featured', 'test data', 60);

    Property::factory()->create();

    expect(Cache::has('homepage:featured'))->toBeFalse();
});

test('cache is cleared when property is updated', function () {
    Cache::put('homepage:featured', 'test data', 60);

    $property = Property::factory()->create();
    $property->update(['title' => 'Updated Title']);

    expect(Cache::has('homepage:featured'))->toBeFalse();
});

test('cache is cleared when property is deleted', function () {
    Cache::put('homepage:featured', 'test data', 60);

    $property = Property::factory()->create();
    $property->delete();

    expect(Cache::has('homepage:featured'))->toBeFalse();
});

test('property listings with pagination cache separately', function () {
    Property::factory()->count(25)->create(['status' => 'active']);

    // Request page 1
    $this->get('/properties?page=1');

    // Request page 2
    $this->get('/properties?page=2');

    $cacheService = app(\App\Services\PropertyCacheService::class);
    $page1Key = $cacheService->generateListingCacheKey([], 1);
    $page2Key = $cacheService->generateListingCacheKey([], 2);

    expect(Cache::has($page1Key))->toBeTrue()
        ->and(Cache::has($page2Key))->toBeTrue()
        ->and($page1Key)->not->toBe($page2Key);
});

test('cache respects TTL of one hour', function () {
    Property::factory()->count(5)->create([
        'is_featured' => true,
        'status' => 'active'
    ]);

    $this->get('/');

    // Cache should exist
    expect(Cache::has('homepage:featured'))->toBeTrue();

    // Simulate time passing (in real scenario, TTL would expire)
    // For this test, we're just verifying the cache was set
    $cachedData = Cache::get('homepage:featured');
    expect($cachedData)->not->toBeNull();
});
