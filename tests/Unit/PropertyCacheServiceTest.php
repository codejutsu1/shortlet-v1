<?php

use App\Services\PropertyCacheService;

test('generates consistent cache key for same filters', function () {
    $service = new PropertyCacheService();
    $filters = ['city' => 'Lagos', 'min_price' => 5000];

    $key1 = $service->generateListingCacheKey($filters, 1);
    $key2 = $service->generateListingCacheKey($filters, 1);

    expect($key1)->toBe($key2);
});

test('generates different keys for different filters', function () {
    $service = new PropertyCacheService();

    $key1 = $service->generateListingCacheKey(['city' => 'Lagos'], 1);
    $key2 = $service->generateListingCacheKey(['city' => 'Abuja'], 1);

    expect($key1)->not->toBe($key2);
});

test('generates different keys for different pages', function () {
    $service = new PropertyCacheService();
    $filters = ['city' => 'Lagos'];

    $key1 = $service->generateListingCacheKey($filters, 1);
    $key2 = $service->generateListingCacheKey($filters, 2);

    expect($key1)->not->toBe($key2);
});

test('cache key includes filter hash', function () {
    $service = new PropertyCacheService();
    $filters = ['city' => 'Lagos', 'min_price' => 5000];

    $key = $service->generateListingCacheKey($filters, 1);

    expect($key)->toContain('properties:filters:')
        ->and($key)->toContain(':page:1');
});

test('empty filters generate valid cache key', function () {
    $service = new PropertyCacheService();

    $key = $service->generateListingCacheKey([], 1);

    expect($key)->toBeString()
        ->and($key)->toContain('properties:filters:');
});
