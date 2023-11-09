<?php

namespace App\Livewire;

use App\Models\Comment;
use App\Models\SurfaceState;
use Livewire\Component;

class Comments extends Component
{
    public $commentable;
    public $comments;

    public $newComment;

    public function mount()
    {
        $this->comments = $this->commentable?->comments;
    }

    public function render()
    {
        return view('livewire.comments');
    }

    public function storeComment()
    {
        $this->commentable->comments()->create([
            'user_id' => auth()->id(),
            'body' => $this->newComment
        ]);

        if (class_basename($this->commentable) == class_basename(SurfaceState::class)){
            $this->commentable->addActivity('new_comment');
        }

        $this->commentable->refresh();

        $this->reset(['newComment']);
    }

    public function removeComment(Comment $comment)
    {
        $comment->delete();
        $this->commentable->refresh();
    }
}
