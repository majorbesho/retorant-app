<?php

namespace App\Policies;

use App\Models\AIAgent;
use App\Models\User;
use App\Models\Restaurant;
use Illuminate\Auth\Access\Response;

class AIAgentPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole('super_admin') || $user->hasRole('restaurant_owner');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, AIAgent $aiAgent): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        // Check if user is linked to the restaurant
        $restaurant = $this->getUserRestaurant($user);
        return $restaurant && $restaurant->id === $aiAgent->restaurant_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('super_admin') || ($user->hasRole('restaurant_owner') && $this->getUserRestaurant($user));
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, AIAgent $aiAgent): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        $restaurant = $this->getUserRestaurant($user);
        return $restaurant && $restaurant->id === $aiAgent->restaurant_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, AIAgent $aiAgent): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        $restaurant = $this->getUserRestaurant($user);
        return $restaurant && $restaurant->id === $aiAgent->restaurant_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, AIAgent $aiAgent): bool
    {
        return $this->delete($user, $aiAgent);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, AIAgent $aiAgent): bool
    {
        return $user->hasRole('super_admin');
    }

    protected function getUserRestaurant(User $user)
    {
        return $user->restaurant;
    }
}
