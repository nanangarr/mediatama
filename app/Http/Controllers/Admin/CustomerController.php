<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class CustomerController extends Controller
{
    /**
     * Display a listing of customers.
     */
    public function index()
    {
        $customers = User::role('customer')
            ->withCount(['accessRequests', 'videoAccesses'])
            ->latest()
            ->paginate(15);

        return view('admin.customer.index', compact('customers'));
    }

    /**
     * Show the form for creating a new customer.
     */
    public function create()
    {
        return view('admin.customer.create');
    }

    /**
     * Store a newly created customer in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string'],
            'is_active' => ['boolean'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'address' => $request->address,
        ]);

        // Assign customer role
        $user->assignRole('customer');

        return redirect()->route('admin.customers.index')
            ->with('success', 'Customer berhasil didaftarkan.');
    }

    /**
     * Display the specified customer.
     */
    public function show(User $customer)
    {
        // Ensure the user is a customer
        if (!$customer->hasRole('customer')) {
            abort(404, 'Customer tidak ditemukan.');
        }

        $customer->load([
            'accessRequests.video',
            'videoAccesses.video',
        ]);

        // Get statistics
        $stats = [
            'total_requests' => $customer->accessRequests()->count(),
            'pending_requests' => $customer->accessRequests()->where('status', 'pending')->count(),
            'approved_requests' => $customer->accessRequests()->where('status', 'approved')->count(),
            'rejected_requests' => $customer->accessRequests()->where('status', 'rejected')->count(),
            'active_accesses' => $customer->videoAccesses()->where('status', 'active')->count(),
        ];

        return view('admin.customer.show', compact('customer', 'stats'));
    }

    /**
     * Show the form for editing the specified customer.
     */
    public function edit(User $customer)
    {
        // Ensure the user is a customer
        if (!$customer->hasRole('customer')) {
            abort(404, 'Customer tidak ditemukan.');
        }

        return view('admin.customer.edit', compact('customer'));
    }

    /**
     * Update the specified customer in storage.
     */
    public function update(Request $request, User $customer)
    {
        // Ensure the user is a customer
        if (!$customer->hasRole('customer')) {
            abort(404, 'Customer tidak ditemukan.');
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $customer->id],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string'],
            'is_active' => ['boolean'],
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
        ];

        // Only update password if provided
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $customer->update($data);

        return redirect()->route('admin.customers.index')
            ->with('success', 'Customer berhasil diperbarui.');
    }

    /**
     * Remove the specified customer from storage.
     */
    public function destroy(User $customer)
    {
        // Ensure the user is a customer
        if (!$customer->hasRole('customer')) {
            abort(404, 'Customer tidak ditemukan.');
        }

        // Check if customer has active access
        $hasActiveAccess = $customer->videoAccesses()
            ->where('status', 'active')
            ->exists();

        if ($hasActiveAccess) {
            return back()->withErrors([
                'error' => 'Tidak dapat menghapus customer yang masih memiliki akses video aktif.'
            ]);
        }

        $customer->delete();

        return redirect()->route('admin.customers.index')
            ->with('success', 'Customer berhasil dihapus.');
    }

    /**
     * Toggle customer active status
     */
    public function toggleStatus(User $customer)
    {
        // Ensure the user is a customer
        if (!$customer->hasRole('customer')) {
            abort(404, 'Customer tidak ditemukan.');
        }

        $customer->update([
            'is_active' => !$customer->is_active
        ]);

        $status = $customer->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return back()->with('success', "Customer berhasil {$status}.");
    }

    /**
     * Send password reset link to customer
     */
    public function sendPasswordReset(User $customer)
    {
        // Ensure the user is a customer
        if (!$customer->hasRole('customer')) {
            abort(404, 'Customer tidak ditemukan.');
        }

        // Send password reset notification
        $customer->sendPasswordResetNotification(
            app('auth.password.broker')->createToken($customer)
        );

        return back()->with('success', 'Link reset password telah dikirim ke email customer.');
    }
}
