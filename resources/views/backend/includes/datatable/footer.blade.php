<div class="card-footer py-0 border-top-0">
    <div class="mt-1 d-flex justify-content-between" style="line-height: 1;">
        <p class="fs--1 align-self-center">
            <select wire:model="perPage" class="form-control form-control-sm d-inline" style="width: auto"
                    id="per_page">
                @foreach($perPageOptions as $option)
                    <option value="{{$option}}">{{$option}}</option>
                @endforeach
            </select>
            <span>Showing {{ $rows->firstItem() }} to {{ $rows->lastItem() }} of {{ number_format($rows->total()) }}
                entries</span>
        </p>
        {{ $rows->appends(request()->query())->links() }}
    </div>
</div>
