@if($bulkDeleteEnabled && user()->can('bulkUpdate', $model) )
    <td>
        <input type="checkbox" wire:model.live="selectedRows" value="{{$row->id}}">
    </td>
@endif
