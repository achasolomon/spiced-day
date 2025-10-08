<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    /**
     * Display a listing of users.
     */
    public function users(Request $request)
    {
        $query = User::query()->with(['consultant']);

        // Filter by user type
        if ($request->has('type') && $request->type !== 'all') {
            $query->where('user_type', $request->type);
        }

        // Filter by status
        if ($request->has('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        // Search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $users = $query->latest()->paginate(15);

        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function createUser()
    {
        return view('admin.users.create');
    }

    /**
     * Store a newly created user in storage.
     */
    public function storeUser(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'user_type' => 'required|in:applicant,consultant,admin',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:255',
            'province' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:20',
            'is_active' => 'boolean',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['email_verified_at'] = now(); // Auto-verify admin-created accounts
        $validated['is_active'] = $request->has('is_active') ? true : false;

        $user = User::create($validated);

        // Create consultant profile if user_type is consultant
        if ($user->user_type === 'consultant') {
            $user->consultant()->create([
                'employee_number' => 'EMP-' . str_pad($user->id, 6, '0', STR_PAD_LEFT),
                'employment_status' => 'active',
                'hire_date' => now(),
            ]);
        }

        return redirect()->route('admin.users.index')
            ->with('success', 'User created successfully!');
    }

    /**
     * Display the specified user.
     */
    public function showUser(User $user)
    {
        $user->load(['applications', 'assignedApplications', 'consultant']);

        return view('admin.users.show', compact('user'));
    }

    /**
     * Get user data as JSON for modal display.
     */
    public function getUserData(User $user)
    {
        $user->load(['applications', 'assignedApplications', 'consultant']);

        $stats = [];
        
        if ($user->user_type === 'applicant') {
            $stats = [
                'applications' => $user->applications()->count(),
                'documents' => \App\Models\Document::where('uploaded_by', $user->id)->count(),
                'appointments' => \App\Models\Appointment::where('applicant_id', $user->id)->count(),
            ];
        } elseif ($user->user_type === 'consultant') {
            $stats = [
                'assigned_applications' => $user->assignedApplications()->count(),
                'completed_applications' => $user->assignedApplications()
                    ->whereIn('status', ['approved', 'rejected'])
                    ->count(),
                'inspections' => \App\Models\Inspection::where('consultant_id', $user->id)->count(),
            ];
        }

        // Get login count from audit logs
        $stats['login_count'] = \App\Models\AuditLog::where('user_id', $user->id)
            ->where('action', 'login')
            ->count();

        // Get recent activity
        $recentActivity = \App\Models\AuditLog::where('user_id', $user->id)
            ->latest()
            ->take(10)
            ->get()
            ->map(function ($log) {
                return [
                    'id' => $log->id,
                    'description' => $log->description,
                    'created_at' => $log->created_at->diffForHumans(),
                ];
            });

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'address' => $user->address,
            'city' => $user->city,
            'province' => $user->province,
            'postal_code' => $user->postal_code,
            'user_type' => $user->user_type,
            'is_active' => $user->is_active,
            'email_verified_at' => $user->email_verified_at,
            'created_at_formatted' => $user->created_at->format('M d, Y'),
            'last_login_formatted' => $user->last_login_at ? $user->last_login_at->format('M d, Y \a\t g:i A') : null,
            'consultant' => $user->consultant,
            'stats' => $stats,
            'recent_activity' => $recentActivity,
        ]);
    }

    /**
     * Show the form for editing the specified user.
     */
    public function editUser(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update the specified user in storage.
     */
   /**
 * Update the specified user in storage.
 */
    public function updateUser(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'phone' => 'nullable|string|max:20',
            'user_type' => 'required|in:applicant,consultant,admin',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:255',
            'province' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:20',
            'is_active' => 'boolean',
        ]);

        // Only update password if provided
        if ($request->filled('password')) {
            $request->validate([
                'password' => 'string|min:8|confirmed',
            ]);
            $validated['password'] = Hash::make($request->password);
        }

        $validated['is_active'] = $request->has('is_active') ? true : false;

        $user->update($validated);

        return redirect()->route('admin.users.index')
            ->with('success', 'User updated successfully!');
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroyUser(User $user)
    {
        // Prevent deleting yourself
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account!');
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'User deleted successfully!');
    }

    /**
     * Activate a user.
     */
    public function activateUser(User $user)
    {
        $user->update(['is_active' => true]);

        return back()->with('success', 'User activated successfully!');
    }

    /**
     * Deactivate a user.
     */
    public function deactivateUser(User $user)
    {
        // Prevent deactivating yourself
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot deactivate your own account!');
        }

        $user->update(['is_active' => false]);

        return back()->with('success', 'User deactivated successfully!');
    }
}