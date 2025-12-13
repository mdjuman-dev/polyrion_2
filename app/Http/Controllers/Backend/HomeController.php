<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Event;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    function dashboard()
    {
        return view('backend.dashboard');
    }

    /**
     * Search for users and events
     */
    public function search(Request $request)
    {
        $query = $request->input('q', '');

        if (empty($query)) {
            return redirect()->back()
                ->with('error', 'Please enter a search term.');
        }

        // First, search for event by title (prioritize events)
        $event = Event::where('title', 'like', "%{$query}%")
            ->orWhere('slug', 'like', "%{$query}%")
            ->first();

        if ($event) {
            return redirect()->route('admin.events.show', $event->id)
                ->with('success', 'Event found!');
        }

        // If no event found, search for user by name, email, or username
        $user = User::where('name', 'like', "%{$query}%")
            ->orWhere('email', 'like', "%{$query}%")
            ->orWhere('username', 'like', "%{$query}%")
            ->first();

        if ($user) {
            // Redirect to dashboard with user info
            // You can create a user details page later and redirect there
            return redirect()->route('admin.backend.dashboard')
                ->with('search_result', [
                    'type' => 'user',
                    'user' => $user,
                    'message' => "User found: {$user->name} ({$user->email})"
                ]);
        }

        // No results found
        return redirect()->back()
            ->with('error', 'No user or event found with that search term.');
    }
}
