<div>
    @foreach($commentable->comments as $comment)
        <div class="message">
            <h6 class="user__name d-flex justify-content-between">
                <span>{{ $comment->user->name }}</span>

                @can('delete', $comment)
                    <a href="#" class="icon" wire:click="removeComment({{ $comment }})">
                        <i class="fal fa-trash text-danger"></i>
                    </a>
                @endcan
            </h6>

            {{ $comment->body }}
        </div>
    @endforeach

    <div class="input-group type__message">
        <input
            wire:model="newComment"
            type="text"
            class="form-control"
            placeholder="Type"
        />
        <button type="submit" class="input-group-text" wire:click="storeComment" @disabled(!$newComment)>
            <x-svg.plus/>
        </button>
    </div>
</div>
