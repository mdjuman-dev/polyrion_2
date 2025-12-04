<?php

namespace App\Livewire\Backend;

use App\Models\MarketComment;
use Livewire\Component;

class CommentManagement extends Component
{
    public $comment;
    public $eventId;

    public function mount($comment, $eventId)
    {
        $this->comment = $comment;
        $this->eventId = $eventId;
    }

    public function toggleStatus()
    {
        $comment = MarketComment::findOrFail($this->comment['id']);
        
        // Toggle is_active status
        $comment->is_active = !$comment->is_active;
        $comment->save();

        $status = $comment->is_active ? 'activated' : 'deactivated';
        
        // Refresh the comment data
        $this->comment = $comment->fresh()->toArray();
        
        // Dispatch event to show toastr notification
        $this->dispatch('show-toastr', [
            'type' => 'success',
            'message' => "Comment {$status} successfully."
        ]);
    }

    public function delete()
    {
        $comment = MarketComment::findOrFail($this->comment['id']);
        
        // Delete all replies first
        $comment->replies()->delete();
        
        // Delete the comment
        $comment->delete();
        
        // Dispatch event to show toastr notification and refresh parent
        $this->dispatch('comment-deleted', [
            'commentId' => $this->comment['id']
        ]);
        
        $this->dispatch('show-toastr', [
            'type' => 'success',
            'message' => 'Comment deleted successfully.'
        ]);
    }

    public function render()
    {
        return view('livewire.backend.comment-management');
    }
}

