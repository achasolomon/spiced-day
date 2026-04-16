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

        // Consultant can view if assigned to appointment or application
        if ($user->user_type === 'consultant') {
            if ($appointment->consultant_id && $appointment->consultant_id == $user->id) {
                return true;
            }
            
            // Load application if not already loaded
            if ($appointment->application_id) {
                if (!$appointment->relationLoaded('application')) {
                    $appointment->load('application');
                }
                
                if ($appointment->application && $appointment->application->consultant_id && $appointment->application->consultant_id == $user->id) {
                    return true;
                }
            }
        }

        // Applicant can view their own
        if ($user->user_type === 'applicant' && $appointment->applicant_id && $appointment->applicant_id == $user->id) {
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

        // Consultant can update their own appointments or appointments for applications they're assigned to
        if ($user->user_type === 'consultant') {
            // Check if consultant is assigned to the appointment
            if ($appointment->consultant_id && $appointment->consultant_id == $user->id) {
                return true;
            }
            
            // Check if consultant is assigned to the application (load relationship if needed)
            if ($appointment->application_id) {
                // Load application if not already loaded
                if (!$appointment->relationLoaded('application')) {
                    $appointment->load('application');
                }
                
                if ($appointment->application && $appointment->application->consultant_id && $appointment->application->consultant_id == $user->id) {
                    return true;
                }
            }
            
            // If appointment has no consultant_id but consultant can view it, allow update
            // This handles cases where appointment was created without explicit consultant assignment
            if (!$appointment->consultant_id && $this->view($user, $appointment)) {
                return true;
            }
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

        // Consultant can cancel their own appointments or appointments for applications they're assigned to (soft delete)
        if ($user->user_type === 'consultant') {
            if ($appointment->consultant_id == $user->id) {
                return true;
            }
            if ($appointment->application && $appointment->application->consultant_id == $user->id) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determine if the user can confirm the appointment.
     */
    public function confirm(User $user, Appointment $appointment): bool
    {
        // Admin can confirm all
        if ($user->user_type === 'admin') {
            return true;
        }

        // Consultant can confirm their appointments or appointments for applications they're assigned to
        if ($user->user_type === 'consultant') {
            // Check if consultant is directly assigned to the appointment
            if ($appointment->consultant_id && $appointment->consultant_id == $user->id) {
                return true;
            }
            
            // Check if consultant is assigned to the application (load relationship if needed)
            if ($appointment->application_id) {
                if (!$appointment->relationLoaded('application')) {
                    $appointment->load('application');
                }
                
                if ($appointment->application && $appointment->application->consultant_id && $appointment->application->consultant_id == $user->id) {
                    return true;
                }
            }
            
            // If appointment has no consultant_id but consultant can view it, allow confirmation
            // This handles cases where appointment was created without explicit consultant assignment
            if (!$appointment->consultant_id && $this->view($user, $appointment)) {
                return true;
            }
        }

        // Applicant can confirm their appointments
        if ($appointment->applicant_id && $appointment->applicant_id == $user->id) {
            return true;
        }

        return false;
    }

    /**
     * Determine if the user can complete the appointment.
     */
    public function complete(User $user, Appointment $appointment): bool
    {
        // Admin can complete all
        if ($user->user_type === 'admin') {
            return true;
        }

        // Consultant can complete appointments they're assigned to or appointments for applications they're assigned to
        if ($user->user_type === 'consultant') {
            // Check if consultant is directly assigned to the appointment
            if ($appointment->consultant_id && $appointment->consultant_id == $user->id) {
                return true;
            }
            
            // Check if consultant is assigned to the application (load relationship if needed)
            if ($appointment->application_id) {
                // Load application if not already loaded
                if (!$appointment->relationLoaded('application')) {
                    $appointment->load('application');
                }
                
                if ($appointment->application && $appointment->application->consultant_id && $appointment->application->consultant_id == $user->id) {
                    return true;
                }
            }
            
            // If appointment has no consultant_id but consultant can view it, allow completion
            // This handles cases where appointment was created without explicit consultant assignment
            if (!$appointment->consultant_id && $this->view($user, $appointment)) {
                return true;
            }
        }

        return false;
    }

}