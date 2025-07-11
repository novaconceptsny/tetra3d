@if($bulkDeleteEnabled && user()->can('bulkUpdate', $model) )
    <td>
        <input type="checkbox" class="bulk-select-checkbox" wire:model.live="selectedRows" value="{{$row->id}}">
    </td>
@endif
