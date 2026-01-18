<?php

use Illuminate\Support\Facades\Route;
use App\Models\Event;

Route::get('/test-tags-fix', function () {
    try {
        $event = Event::with('tags')->find(2883);
        
        if (!$event) {
            return response()->json([
                'status' => 'error',
                'message' => 'Event not found'
            ]);
        }
        
        return response()->json([
            'status' => 'success',
            'event_id' => $event->id,
            'event_title' => $event->title,
            'tags_count' => $event->tags->count(),
            'tags' => $event->tags->map(function($tag) {
                return [
                    'id' => $tag->id,
                    'label' => $tag->label,
                    'slug' => $tag->slug
                ];
            })
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ], 500);
    }
});

