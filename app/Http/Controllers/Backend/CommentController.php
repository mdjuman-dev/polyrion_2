<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\MarketComment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    /**
     * Delete a comment
     */
    public function destroy($id)
    {
        $comment = MarketComment::findOrFail($id);
        $eventId = $comment->market_id; // market_id is actually event_id

        // Delete all replies first
        $comment->replies()->delete();

        // Delete the comment
        $comment->delete();

        return redirect()
            ->route('admin.events.show', $eventId)
            ->with('success', 'Comment deleted successfully.');
    }

    /**
     * Toggle comment active status (inactive/active)
     */
    public function toggleStatus($id)
    {
        $comment = MarketComment::findOrFail($id);
        $eventId = $comment->market_id;

        // Toggle is_active status
        $comment->is_active = !$comment->is_active;
        $comment->save();

        $status = $comment->is_active ? 'activated' : 'deactivated';

        return redirect()
            ->route('admin.events.show', $eventId)
            ->with('success', "Comment {$status} successfully.");
    }
}
