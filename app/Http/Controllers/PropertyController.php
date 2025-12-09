<?php

namespace App\Http\Controllers;

use App\Models\Property;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PropertyController extends Controller
{
    /**
     * Display the homepage with featured properties.
     */
    public function homepage()
    {
        // Get featured properties or fallback to recent active properties
        $featuredProperties = Property::where('status', 'active')
            ->where(function ($query) {
                $query->where('is_featured', true)
                    ->orWhere('created_at', '>=', now()->subDays(30));
            })
            ->with(['images' => fn($q) => $q->where('is_primary', true)->orWhereNull('is_primary')->orderBy('is_primary', 'desc')])
            ->limit(8)
            ->get();

        return Inertia::render('Welcome', [
            'featuredProperties' => $featuredProperties,
        ]);
    }

    /**
     * Display a listing of properties.
     */
    public function index(Request $request)
    {
        $query = Property::query()
            ->with(['images', 'amenities'])
            ->where('status', 'active');

        // Filter by city
        if ($request->filled('city')) {
            $query->where('city', $request->city);
        }

        // Filter by price range
        if ($request->filled('min_price')) {
            $query->where('price_per_night', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price_per_night', '<=', $request->max_price);
        }

        // Filter by guest count
        if ($request->filled('guests')) {
            $query->where('max_guests', '>=', $request->guests);
        }

        // Filter by amenities (all selected amenities must be present)
        if ($request->filled('amenities')) {
            $amenityIds = is_array($request->amenities)
                ? $request->amenities
                : explode(',', $request->amenities);

            foreach ($amenityIds as $amenityId) {
                $query->whereHas('amenities', function ($q) use ($amenityId) {
                    $q->where('amenities.id', $amenityId);
                });
            }
        }

        // Sorting
        $sortBy = $request->input('sort_by', 'newest');
        switch ($sortBy) {
            case 'price_low':
                $query->orderBy('price_per_night', 'asc');
                break;
            case 'price_high':
                $query->orderBy('price_per_night', 'desc');
                break;
            case 'rating':
                $query->withAvg('reviews', 'rating')
                    ->orderByDesc('reviews_avg_rating');
                break;
            case 'newest':
            default:
                $query->orderByDesc('created_at');
                break;
        }

        $properties = $query->paginate(12)->withQueryString();

        // Get filter options
        $cities = Property::where('status', 'active')
            ->select('city')
            ->distinct()
            ->pluck('city')
            ->sort()
            ->values();

        $amenities = \App\Models\Amenity::orderBy('name')->get();

        return Inertia::render('Properties/Index', [
            'properties' => $properties,
            'cities' => $cities,
            'amenities' => $amenities,
            'filters' => $request->only([
                'city',
                'check_in',
                'check_out',
                'guests',
                'min_price',
                'max_price',
                'amenities',
                'sort_by'
            ]),
        ]);
    }

    /**
     * Display the specified property.
     */
    public function show(Request $request, Property $property)
    {
        // Get sort parameter for reviews
        $reviewSort = $request->input('review_sort', 'newest');

        // Load relationships including reviews with sorting
        $property->load([
            'images',
            'amenities',
            'user',
            'reviews' => function ($query) use ($reviewSort) {
                $query->with('user');

                // Apply sorting
                switch ($reviewSort) {
                    case 'highest_rated':
                        $query->orderByDesc('rating')->orderByDesc('created_at');
                        break;
                    case 'lowest_rated':
                        $query->orderBy('rating')->orderByDesc('created_at');
                        break;
                    case 'newest':
                    default:
                        $query->orderByDesc('created_at');
                        break;
                }

                $query->limit(10); // Show latest 10 reviews
            }
        ]);

        // Calculate average rating and review count
        $averageRating = $property->reviews()->avg('rating');
        $reviewCount = $property->reviews()->count();

        // Get rating breakdown (count for each star rating)
        $ratingBreakdown = [
            5 => $property->reviews()->where('rating', 5)->count(),
            4 => $property->reviews()->where('rating', 4)->count(),
            3 => $property->reviews()->where('rating', 3)->count(),
            2 => $property->reviews()->where('rating', 2)->count(),
            1 => $property->reviews()->where('rating', 1)->count(),
        ];

        // Get similar properties (same city, exclude current)
        $similarProperties = Property::where('city', $property->city)
            ->where('id', '!=', $property->id)
            ->where('status', 'active')
            ->with(['images'])
            ->limit(4)
            ->get();

        return Inertia::render('Properties/Show', [
            'property' => $property,
            'averageRating' => $averageRating ? round($averageRating, 1) : null,
            'reviewCount' => $reviewCount,
            'ratingBreakdown' => $ratingBreakdown,
            'similarProperties' => $similarProperties,
            'reviewSort' => $reviewSort,
        ]);
    }
}
