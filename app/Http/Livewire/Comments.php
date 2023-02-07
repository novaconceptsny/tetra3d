<?php

namespace App\Http\Livewire;

use App\Models\Comment;
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

        $this->commentable->refresh();

        $this->reset(['newComment']);
    }

    public function removeComment(Comment $comment)
    {
        $comment->delete();
        $this->commentable->refresh();
    }
}
