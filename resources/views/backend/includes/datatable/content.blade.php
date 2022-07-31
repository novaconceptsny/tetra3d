@if($bulkDeleteEnabled)
    <td>
        <input type="checkbox" wire:model="selectedRows" value="{{$row->id}}">
    </td>
@endif

@foreach($columns as $col_name => $column)
    @if($column['visible'] && ($column['render'] ?? true))
        <td class="td" data-column="{{ $col_name }}" >{!! $row->$col_name !!}</td>
    @endif
@endforeach
