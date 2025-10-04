<?php
// app/Policies/AppointmentPolicy.php

namespace App\Policies;

use App\Models\Appointment;
use App\Models\User;

class AppointmentPolicy
{
    /**
     * Determine if the user can view any appointments.
     */
    public function viewAny(User $user): bool
    {
        // All authenticated users can view their own appointments
        return true;
    }

    /**
     * Determine if the user can view a specific appointment.
     */
    public function view(User $user, Appointment $appointment): bool
    {
        // Admin can view all
        if ($user->user_type === 'admin') {
            return true;
        }

        // Consultant can view if assigned
        if ($user->user_type === 'consultant' && $appointment->consultant_id === $user->id) {
            return true;
        }

        // Applicant can view their own
        if ($user->user_type === 'applicant' && $appointment->applicant_id === $user->id) {
            return true;
        }

        return false;
    }

    /**
     * Determine if the user can create appointments.
     */
    public function create(User $user): bool
    {
        // Consultants and admins can create appointments
        return in_array($user->user_type, ['consultant', 'admin']);
    }

    /**
     * Determine if the user can update the appointment.
     */
    public function update(User $user, Appointment $appointment): bool
    {
        // Admin can update all
        if ($user->user_type === 'admin') {
            return true;
        }

        // Consultant can update their own appointments
        if ($user->user_type === 'consultant' && $appointment->consultant_id === $user->id) {
            return true;
        }

        return false;
    }

    /**
     * Determine if the user can delete the appointment.
     */
    public function delete(User $user, Appointment $appointment): bool
    {
        // Only admin can delete
        if ($user->user_type === 'admin') {
            return true;
        }

        // Consultant can cancel their own appointments (soft delete)
        if ($user->user_type === 'consultant' && $appointment->consultant_id === $user->id) {
            return true;
        }

        return false;
    }

    /**
     * Determine if the user can confirm the appointment.
     */
    public function confirm(User $user, Appointment $appointment): bool
    {
        // Consultant can confirm their appointments
        if ($user->id === $appointment->consultant_id) {
            return true;
        }

        // Applicant can confirm their appointments
        if ($user->id === $appointment->applicant_id) {
            return true;
        }

        return false;
    }

    /**
     * Determine if the user can complete the appointment.
     */
    public function complete(User $user, Appointment $appointment): bool
    {
        // Only consultant or admin can mark as completed
        return $user->user_type === 'admin' || 
               ($user->user_type === 'consultant' && $appointment->consultant_id === $user->id);
    }
}