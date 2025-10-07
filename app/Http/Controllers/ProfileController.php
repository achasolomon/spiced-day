<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $user = $request->user();
        
        // Load relationships based on user type
        if ($user->isConsultant()) {
            $user->load('consultant');
        } elseif ($user->isApplicant()) {
            $user->load('applications');
        }

        return view('profile.edit', [
            'user' => $user,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();
        
        // Base validation rules for all users
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
        ];

        // Add consultant-specific validation rules
        if ($user->isConsultant()) {
            $rules = array_merge($rules, [
                'work_phone' => 'nullable|string|max:20',
                'bio' => 'nullable|string',
                'specializations' => 'nullable|string',
                'languages' => 'nullable|string',
                'max_concurrent_applications' => 'nullable|integer|min:1|max:50',
                'accepts_new_applications' => 'nullable|boolean',
                'emergency_contact_name' => 'nullable|string|max:255',
                'emergency_contact_phone' => 'nullable|string|max:20',
            ]);
        }

        $validated = $request->validate($rules);

        // Update base user information
        $user->fill([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
        ]);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        // Update consultant-specific information
        if ($user->isConsultant() && $user->consultant) {
            $consultantData = [
                'work_phone' => $validated['work_phone'] ?? null,
                'bio' => $validated['bio'] ?? null,
                'max_concurrent_applications' => $validated['max_concurrent_applications'] ?? 10,
                'accepts_new_applications' => $validated['accepts_new_applications'] ?? true,
                'emergency_contact_name' => $validated['emergency_contact_name'] ?? null,
                'emergency_contact_phone' => $validated['emergency_contact_phone'] ?? null,
            ];

            // Convert comma-separated strings to arrays
            if (!empty($validated['specializations'])) {
                $consultantData['specializations'] = array_map('trim', explode(',', $validated['specializations']));
            }
            
            if (!empty($validated['languages'])) {
                $consultantData['languages'] = array_map('trim', explode(',', $validated['languages']));
            }

            $user->consultant->update($consultantData);
        }

        return Redirect::route('profile.edit')->with('success', 'Profile updated successfully!');
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return Redirect::route('profile.edit')->with('success', 'Password updated successfully!');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}