<thead class="bg-200 text-900">
<tr class="dt-row">

    @foreach($columns as $column => $header)
        @php
            $moveToStart = isset($header['move_to_start']) ? "data-move-to-start=true" : "";
            $moveAfter = isset($header['move_after']) ? "data-move-after={$header['move_after']}" : "";
            $moveBefore = isset($header['move_before']) ? "data-move-before={$header['move_before']}" : "";
        @endphp

        @if($header['visible'])
            <th class="cursor-pointer td {{$header['th-classes'] ?? ''}}" scope="col" wire:click="sort('{{$column}}')"
                data-column="{{ $column }}" {{ $moveAfter }} {{ $moveBefore }} {{$moveToStart}}>
                <span class="d-flex w-full justify-content-center align-items-center">
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
