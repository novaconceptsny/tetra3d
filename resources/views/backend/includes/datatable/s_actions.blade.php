<x-backend::dropdown.container>
    <x-backend::dropdown.item :route="route('backend.sculptures.edit', $row->id)">
        <i class="fal fa-pen mr-1"></i> {{ __('Edit') }}
    </x-backend::dropdown.item>
    <x-backend::dropdown.item
        class="text-danger" data-bs-toggle="modal" data-bs-target="#confirm_{{$label}}_{{ $row->id }}">
        <i class="fa fa-trash mr-1"></i> {{ __('Delete') }}
    </x-backend::dropdown.item>
</x-backend::dropdown.container>
<x-backend::modals.confirm
    :route="route($routes['delete'], $row->id)" :model="$row"
    :button="false">
</x-backend::modals.confirm>
