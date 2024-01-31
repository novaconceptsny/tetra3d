<div class="me-1">
    <select wire:model.live="perPage" class="form-control  rounded-0 border-black" id="per_page">
        @foreach($perPageOptions as $option)
            <option value="{{$option}}">{{$option}}</option>
        @endforeach
    </select>
</div>
<div class="me-1">
    <input wire:model.live="search" class="form-control rounded-0 border-black search text-black" type="text" placeholder="{{ __('Search') }}">
</div>
