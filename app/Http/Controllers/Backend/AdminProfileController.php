<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AdminProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    /**
     * Show the admin profile
     */
    public function show()
    {
        $admin = Auth::guard('admin')->user();
        $admin->load('roles', 'permissions');
        
        return view('backend.profile.show', compact('admin'));
    }

    /**
     * Show the form for editing the admin profile
     */
    public function edit()
    {
        $admin = Auth::guard('admin')->user();
        return view('backend.profile.edit', compact('admin'));
    }

    /**
     * Update the admin profile
     */
    public function update(Request $request)
    {
        $admin = Auth::guard('admin')->user();

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:admins,email,' . $admin->id,
            'password' => 'nullable|string|min:8|confirmed',
            'current_password' => 'required_with:password|string',
        ], [
            'name.required' => 'Name is required.',
            'email.required' => 'Email is required.',
            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'This email is already taken.',
            'password.min' => 'Password must be at least 8 characters.',
            'password.confirmed' => 'Password confirmation does not match.',
            'current_password.required_with' => 'Current password is required to change password.',
        ]);

        // Validate current password if password is being changed
        if ($request->filled('password')) {
            if (!Hash::check($request->current_password, $admin->password)) {
                $validator->errors()->add('current_password', 'Current password is incorrect.');
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }
        }

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $admin->name = $request->name;
            $admin->email = $request->email;

            if ($request->filled('password')) {
                $admin->password = Hash::make($request->password);
            }

            $admin->save();

            return redirect()->route('admin.profile.show')
                ->with('success', 'Profile updated successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to update profile: ' . $e->getMessage())
                ->withInput();
        }
    }
}
