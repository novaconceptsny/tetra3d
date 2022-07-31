<div class="me-1">
    <select wire:model="perPage" class="form-control" id="per_page">
        @foreach($perPageOptions as $option)
            <option value="{{$option}}">{{$option}}</option>
        @endforeach
    </select>
</div>
<div class="ml-auto">
    <input wire:model="search" class="form-control" type="text" placeholder="{{ __('Search') }}">
</div>
