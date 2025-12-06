<?php

namespace App\Livewire\Backend;

use App\Models\MarketComment;
use Livewire\Component;

class CommentActions extends Component
{
    public $commentId;
    public $isActive;
    public $eventId;

    public function mount($commentId, $isActive, $eventId)
    {
        $this->commentId = $commentId;
        $this->isActive = $isActive ?? true;
        $this->eventId = $eventId;
    }

    public function toggleStatus()
    {
        $comment = MarketComment::findOrFail($this->commentId);

        // Toggle is_active status
        $comment->is_active = !$comment->is_active;
        $comment->save();

        // Update local state
        $this->isActive = $comment->is_active;

        $status = $comment->is_active ? 'activated' : 'deactivated';

        // Show toastr notification
        $this->dispatch('showToastr', type: 'success', message: "Comment {$status} successfully.");
    }

    public function delete()
    {
        $comment = MarketComment::findOrFail($this->commentId);

        // Delete all replies first
        $comment->replies()->delete();

        // Delete the comment
        $comment->delete();

        // Show toastr notification
        $this->dispatch('showToastr', type: 'success', message: 'Comment deleted successfully.');

        // Emit event to refresh parent component
        $this->dispatch('commentDeleted', $this->commentId);

        // Redirect after deletion
        $this->redirect(route('admin.events.show', $this->eventId), navigate: false);
    }

    public function render()
    {
        return view('livewire.backend.comment-actions');
    }
}
