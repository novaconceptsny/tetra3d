<x-wire-elements-pro::bootstrap.modal on-submit="sync">
    <x-slot name="title">Collector Tools</x-slot>
    <div>
        <div class="row mb-2 g-2">
            <x-backend::inputs.radio label="Sync By">
                <x-backend::inputs.radio-option name="syncBy" label="Object" value="object" wire:model.live="syncBy" :selected="$syncBy"/>
                <x-backend::inputs.radio-option name="syncBy" label="Collection" value="collection" wire:model.live="syncBy" :selected="$syncBy"/>
            </x-backend::inputs.radio>

            @if($syncBy == 'collection')
                <x-backend::inputs.text name="collection_id" wire:model.live="collectionId"/>
            @else
                <x-backend::inputs.text name="object_id" wire:model.live="objectId"/>
            @endif
        </div>

        @if($output)
            <div class="row">
                <div class="col-12">
                    <div class="alert alert-{{ $success ? 'success' : 'danger' }}" role="alert">
                        @if($success)
                            <i class="fal fa-check-circle"></i>
                        @endif
                        {!! $output !!}
                    </div>
                </div>
            </div>
        @endif

        @if($artwork)
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="row g-0 align-items-center border border-success g-0 p-2 rounded">
                            <div class="col-md-4">
                                <img src="{{ $artwork->image_url }}" class="card-img" alt="">
                            </div>
                            <div class="col-md-8">
                                <div class="card-body">
                                    <h4 class="card-title text-center">{{ $artwork->name }}</h4>
                                    <ul class="list-unstyled mb-0">
                                        <li>
                                            <p class="mb-2"><span class="fw-bold me-2">Object
                                                    ID:</span> {{ $artwork->collector_object_id }}</p>
                                            <p class="mb-2"><span
                                                    class="fw-bold me-2">Artist:</span> {{ $artwork->artist }}</p>
                                            <p class="mb-2"><span
                                                    class="fw-bold me-2">Dimension:</span> {{ $artwork->dimensions }}
                                            </p>
                                            <p class="mb-2"><span
                                                    class="fw-bold me-2">Type:</span> {{ $artwork->type }}</p>
                                            <p class="mb-2"><span
                                                    class="fw-bold me-2">Collection:</span> {{ $artwork->collection->name }}
                                            </p>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
    <x-slot name="buttons">
        <button type="submit" class="btn btn-sm btn-success me-2"
                wire:click="sync" wire:loading.class="disabled"
                wire:loading.attr="disabled">
            <span wire:loading.remove wire:target="sync">Sync</span>
            <span wire:loading wire:target="sync">Syncing...</span>
        </button>

        <button class="btn btn-sm btn-primary" type="button" wire:click="$dispatch('modal.close')">
            {{ __('Cancel') }}
        </button>
    </x-slot>
</x-wire-elements-pro::bootstrap.modal>
