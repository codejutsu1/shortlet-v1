<?php

namespace App\Policies;

use App\Models\Booking;
use App\Models\User;

class ReviewPolicy
{
    /**
     * Determine if the user can review the booking.
     */
    public function review(User $user, Booking $booking): bool
    {
        // User must own the booking to review it
        return $user->id === $booking->user_id;
    }
}
