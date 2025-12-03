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
            ->with(['images', 'amenities', 'user'])
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

        // Filter by amenities
        if ($request->filled('amenities')) {
            $amenityIds = explode(',', $request->amenities);
            $query->whereHas('amenities', function ($q) use ($amenityIds) {
                $q->whereIn('amenities.id', $amenityIds);
            });
        }

        $properties = $query->paginate(12);

        // Get unique cities for filter
        $cities = Property::select('city')->distinct()->pluck('city');

        return Inertia::render('Properties/Index', [
            'properties' => $properties,
            'cities' => $cities,
            'filters' => $request->only(['city', 'min_price', 'max_price', 'amenities']),
        ]);
    }

    /**
     * Display the specified property.
     */
    public function show(Property $property)
    {
        $property->load(['images', 'amenities', 'user']);

        // Get similar properties (same city, exclude current)
        $similarProperties = Property::where('city', $property->city)
            ->where('id', '!=', $property->id)
            ->where('status', 'active')
            ->with(['images'])
            ->limit(4)
            ->get();

        return Inertia::render('Properties/Show', [
            'property' => $property,
            'similarProperties' => $similarProperties,
        ]);
    }
}
