@if($columnsToggleable)
<div class="mt-3">
    <h5>Visible Columns</h5>
    <div>
        @foreach($columns as $column => $header)
            <button class="btn btn-{{$header['visible'] ? '' : 'outline-'}}primary btn-sm me-1"
                    wire:click="changeVisibility('{{$column}}')">{{ __($header['name']) }}</button>
        @endforeach
    </div>
</div>
@endif
