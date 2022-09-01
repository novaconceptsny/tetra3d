@if($bulkDeleteEnabled)
    <td>
        <input type="checkbox" wire:model="selectedRows" value="{{$row->id}}">
    </td>
@endif
