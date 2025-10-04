<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */

    public function profile()
{
    return view('consultant.profile');
}

public function updateProfile(Request $request)
{
    $validated = $request->validate([
        'work_phone' => 'nullable|string|max:20',
        'bio' => 'nullable|string',
        'specializations' => 'nullable|string',
        'languages' => 'nullable|string',
        'max_concurrent_applications' => 'required|integer|min:1|max:50',
        'accepts_new_applications' => 'boolean',
        'emergency_contact_name' => 'nullable|string|max:255',
        'emergency_contact_phone' => 'nullable|string|max:20',
    ]);

    // Convert comma-separated strings to arrays
    if (!empty($validated['specializations'])) {
        $validated['specializations'] = array_map('trim', explode(',', $validated['specializations']));
    }
    
    if (!empty($validated['languages'])) {
        $validated['languages'] = array_map('trim', explode(',', $validated['languages']));
    }

    auth()->user()->consultant->update($validated);

    return redirect()->route('consultant.profile')
        ->with('success', 'Profile updated successfully!');
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
