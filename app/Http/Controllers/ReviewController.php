<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Review;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class ReviewController extends Controller
{
    use AuthorizesRequests;
    /**
     * Show the review creation form
     */
    public function create(Booking $booking)
    {
        // Authorization check
        $this->authorize('review', $booking);

        // Check if booking is completed
        if ($booking->status !== 'completed') {
            return redirect()->back()
                ->withErrors(['message' => 'You can only review completed bookings.']);
        }

        // Check if already reviewed
        if ($booking->review()->exists()) {
            return redirect()->route('properties.show', $booking->property_id)
                ->with('message', 'You have already reviewed this property.');
        }

        // Check if checkout date has passed
        if ($booking->check_out->isFuture()) {
            return redirect()->back()
                ->withErrors(['message' => 'You can only review after your checkout date.']);
        }

        // Load booking with property details
        $booking->load(['property.images', 'property.amenities']);

        return Inertia::render('Reviews/Create', [
            'booking' => $booking,
        ]);
    }

    /**
     * Store a newly created review
     */
    public function store(Request $request, Booking $booking)
    {
        // Authorization check
        $this->authorize('review', $booking);

        // Validate request
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|min:10|max:1000',
        ]);

        // Eligibility checks
        if ($booking->status !== 'completed') {
            return back()->withErrors(['message' => 'Invalid booking status.']);
        }

        if ($booking->review()->exists()) {
            return back()->withErrors(['message' => 'You have already reviewed this property.']);
        }

        if ($booking->check_out->isFuture()) {
            return back()->withErrors(['message' => 'You can only review after your checkout date.']);
        }

        // Create review
        $review = Review::create([
            'user_id' => auth()->id(),
            'property_id' => $booking->property_id,
            'booking_id' => $booking->id,
            'rating' => $validated['rating'],
            'comment' => $validated['comment'],
        ]);

        // Log review creation
        Log::info('Review created', [
            'review_id' => $review->id,
            'user_id' => auth()->id(),
            'property_id' => $booking->property_id,
            'rating' => $review->rating,
        ]);

        return redirect()
            ->route('properties.show', $booking->property_id)
            ->with('success', 'Thank you for your review!');
    }
}
