@props([
    'media' => null,
    'name' => null,
    'rules' => '',
])

<div class="d-flex">

    @if($media)
        <div class="media-library-thumb m-0 me-2">
            <img src="{{ $media->getUrl() }}" alt="s4001.jpg"  class="media-library-thumb-img" style="object-fit: fill;">
        </div>
    @endif

    <div class="flex-grow-1">
        <x-media-library-attachment
            :name="$name" :rules="$rules"
        />
    </div>
</div>
