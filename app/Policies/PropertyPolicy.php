<?php

namespace App\Policies;

use App\Models\Property;
use App\Models\User;

class PropertyPolicy
{
    /**
     * Determine if the user can view any properties.
     */
    public function viewAny(?User $user): bool
    {
        // Anyone can browse properties
        return true;
    }

    /**
     * Determine if the user can view the property.
     */
    public function view(?User $user, Property $property): bool
    {
        // Active properties are public, others require admin
        return $property->status === 'active' || ($user && ($user->is_admin ?? false));
    }

    /**
     * Determine if the user can create properties.
     */
    public function create(User $user): bool
    {
        return $user->is_admin ?? false;
    }

    /**
     * Determine if the user can update the property.
     */
    public function update(User $user, Property $property): bool
    {
        return $user->is_admin ?? false;
    }

    /**
     * Determine if the user can delete the property.
     */
    public function delete(User $user, Property $property): bool
    {
        return $user->is_admin ?? false;
    }

    /**
     * Determine if the user can restore the property.
     */
    public function restore(User $user, Property $property): bool
    {
        return $user->is_admin ?? false;
    }

    /**
     * Determine if the user can permanently delete the property.
     */
    public function forceDelete(User $user, Property $property): bool
    {
        return $user->is_admin ?? false;
    }
}
