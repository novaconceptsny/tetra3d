@foreach($columns as $col_name => $column)
    @if($column['visible'] && ($column['render'] ?? true))
        <td class="td {{ $column['td-classes'] ?? '' }}" data-column="{{ $col_name }}" >{!! $row->$col_name !!}</td>
    @endif
@endforeach
