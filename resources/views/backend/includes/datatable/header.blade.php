<thead class="bg-200 text-900">
<tr class="dt-row">
    @if($bulkDeleteEnabled)
        <th></th>
    @endif

    @foreach($columns as $column => $header)
        @php
            $moveAfter = isset($header['move_after']) ? "data-move-after={$header['move_after']}" : "";
            $moveBefore = isset($header['move_before']) ? "data-move-before={$header['move_before']}" : "";
        @endphp

        @if($header['visible'])
            <th class="cursor-pointer td" scope="col" wire:click="sort('{{$column}}')" data-column="{{ $column }}" {{ $moveAfter }} {{ $moveBefore }}>
                <span class="d-flex">
                    <span> {{ __($header['name']) }}</span>
                    @if($header['sortable'] ?? false)
                        <x-backend::sort-icon
                            :direction="$sortOrder"
                            :sorted="$sortBy == ($columns[$column]['sort_by'] ?? $column)"
                        />
                    @endif
                </span>
            </th>
        @endif
    @endforeach
    <th></th>
</tr>
</thead>
