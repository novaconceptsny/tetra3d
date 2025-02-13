@props([
    'media' => null,
    'name' => null,
    'rules' => '',
    'showFilename' => true,
    'id' => null
])

<div class="d-flex" id="{{ $id }}">

    @if($media)
        <div class="media-library-thumb m-0 me-2">
            @if(Str::startsWith($media->mime_type, 'image/'))
                <img src="{{ $media->getUrl() }}" 
                     alt="{{ $media->file_name }}"  
                     class="media-library-thumb-img" 
                     style="object-fit: fill">
                @if($showFilename)
                    <span class="fs-6" style="white-space: nowrap">{{ $media->file_name }}</span>
                @endif
            @endif
        </div>
    @endif

    <div class="flex-grow-1">
        <x-media-library-attachment
            :name="$name" :rules="$rules"
        />
    </div>
</div>
