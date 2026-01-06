<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\SocialMediaLink;
use Illuminate\Http\Request;

class SocialMediaController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:manage social media,admin');
    }

    public function index()
    {
        $links = SocialMediaLink::orderBy('platform')->get();
        return view('backend.social-media.index', compact('links'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'links' => 'required|array',
            'links.*.url' => 'nullable|url|max:500',
            'links.*.status' => 'required|in:active,inactive',
        ]);

        foreach ($request->links as $platform => $data) {
            $link = SocialMediaLink::where('platform', $platform)->first();
            if ($link) {
                $link->update([
                    'url' => !empty($data['url']) ? $data['url'] : null,
                    'status' => $data['status'],
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Social media settings updated successfully!',
        ]);
    }
}
