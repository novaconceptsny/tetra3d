<div class="col-3 side-col" :class="{ 'd-none': sidebar === 'editor' }">
    <div class="top-div">
        <div class="text">
            <h6 class="mb-0">{{ __('Comments') }}</h6>
        </div>
        <div class="outside">
            <div class="line"></div>
            <button class="editor-comment-btn btn" @click="sidebar = 'editor'">
                <i class="fal fa-edit"></i>
                <span>{{ __('Editor') }}</span>
            </button>
        </div>
    </div>
    <div class="comment-div">
        <div class="comments">
            @if($commentable)
                @foreach($commentable->comments as $comment)
                    <div class="comment">
                        <img src="{{ $comment->user->avatar_url }}" alt="{{ $comment->user->name }}"/>
                        <div class="text">
                            <h6>{{ $comment->body }}</h6>
                            <p>
                                {{ $comment->user->name }} | {{ $comment->created_at->format('d/m/Y') }}
                                @can('delete', $comment)
                                    <a href="#" class="ms-3" wire:click="removeComment({{ $comment }})">
                                        <i class="fal fa-trash text-danger"></i>
                                    </a>
                                @endcan
                            </p>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>

    @if($commentable)
        <div class="send-comment input-group">
            <input
                wire:model="newComment"
                type="text"
                class="form-control"
                placeholder="Add new comment"
            />
            <button class="send-btn" type="button" @keydown.enter="$wire.storeComment"
                    wire:click="storeComment" @disabled(!$newComment)>
                {{ __('Send') }}
                <i class="fal fa-paper-plane ms-2"></i>
            </button>
        </div>
    @endif
</div>
