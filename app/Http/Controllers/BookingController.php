<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Property;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class BookingController extends Controller
{
    /**
     * Display a listing of the user's bookings.
     */
    public function index()
    {
        $bookings = Auth::user()->bookings()
            ->with(['property.images'])
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('Bookings/Index', [
            'bookings' => $bookings,
        ]);
    }

    /**
     * Store a newly created booking.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'property_id' => 'required|exists:properties,id',
            'check_in' => 'required|date|after:today',
            'check_out' => 'required|date|after:check_in',
            'guests' => 'required|integer|min:1',
        ]);

        $property = Property::findOrFail($validated['property_id']);

        // Check if guests exceed max
        if ($validated['guests'] > $property->max_guests) {
            return back()->withErrors(['guests' => 'Number of guests exceeds property limit.']);
        }

        // Check availability (no overlapping bookings)
        $hasConflict = Booking::where('property_id', $property->id)
            ->where('status', '!=', 'cancelled')
            ->where(function ($query) use ($validated) {
                $query->whereBetween('check_in', [$validated['check_in'], $validated['check_out']])
                    ->orWhereBetween('check_out', [$validated['check_in'], $validated['check_out']])
                    ->orWhere(function ($q) use ($validated) {
                        $q->where('check_in', '<=', $validated['check_in'])
                            ->where('check_out', '>=', $validated['check_out']);
                    });
            })
            ->exists();

        if ($hasConflict) {
            return back()->withErrors(['dates' => 'Property is not available for selected dates.']);
        }

        // Calculate total price
        $checkIn = Carbon::parse($validated['check_in']);
        $checkOut = Carbon::parse($validated['check_out']);
        $nights = $checkIn->diffInDays($checkOut);
        $totalPrice = $property->price_per_night * $nights;

        // Create booking
        $booking = Booking::create([
            'property_id' => $property->id,
            'user_id' => Auth::id(),
            'check_in' => $validated['check_in'],
            'check_out' => $validated['check_out'],
            'guests' => $validated['guests'],
            'total_price' => $totalPrice,
            'status' => 'pending',
        ]);

        Log::info('Booking created successfully', [
            'booking_id' => $booking->id,
            'user_id' => Auth::id(),
            'property_id' => $property->id,
            'check_in' => $validated['check_in'],
            'check_out' => $validated['check_out'],
            'total_price' => $totalPrice,
            'nights' => $nights
        ]);

        return redirect()->route('bookings.show', $booking)
            ->with('success', 'Booking created successfully!');
    }

    /**
     * Display the specified booking.
     */
    public function show(Booking $booking)
    {
        // Ensure user owns the booking
        if ($booking->user_id !== Auth::id()) {
            abort(403);
        }

        $booking->load(['property.images', 'user']);

        return Inertia::render('Bookings/Show', [
            'booking' => $booking,
        ]);
    }

    /**
     * Cancel the specified booking.
     */
    public function cancel(Booking $booking)
    {
        // Ensure user owns the booking
        if ($booking->user_id !== Auth::id()) {
            abort(403);
        }

        // Can only cancel pending or confirmed bookings
        if (!in_array($booking->status, ['pending', 'confirmed'])) {
            return back()->withErrors(['error' => 'Cannot cancel this booking.']);
        }

        $booking->update(['status' => 'cancelled']);

        Log::info('Booking cancelled', [
            'booking_id' => $booking->id,
            'user_id' => Auth::id(),
            'property_id' => $booking->property_id,
            'refund_amount' => $booking->total_price
        ]);

        return redirect()->route('bookings.index')
            ->with('success', 'Booking cancelled successfully.');
    }
}
