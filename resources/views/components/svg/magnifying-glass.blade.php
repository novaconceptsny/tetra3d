@props([
    'color' => '#CCCCCC',
    'strokeWidth' => 2,
    'size' => ''
])
@if($size == 'small')
    <svg
        width="17"
        height="17"
        viewBox="0 0 17 17"
        fill="none"
        xmlns="http://www.w3.org/2000/svg"
    >
        <path
            d="M7.6667 14.3334C11.3486 14.3334 14.3334 11.3486 14.3334 7.6667C14.3334 3.98478 11.3486 1 7.6667 1C3.98478 1 1 3.98478 1 7.6667C1 11.3486 3.98478 14.3334 7.6667 14.3334Z"
            stroke="black"
            stroke-width="1.2"
            stroke-linecap="round"
            stroke-linejoin="round"
        />
        <path
            d="M16 16L12.375 12.375"
            stroke="black"
            stroke-width="1.2"
            stroke-linecap="round"
            stroke-linejoin="round"
        />
    </svg>
@else
    <svg
        width="24"
        height="24"
        viewBox="0 0 24 24"
        fill="none"
        xmlns="http://www.w3.org/2000/svg"
    >
        <path
            d="M11 19C15.4183 19 19 15.4183 19 11C19 6.58172 15.4183 3 11 3C6.58172 3 3 6.58172 3 11C3 15.4183 6.58172 19 11 19Z"
            stroke="{{$color}}"
            stroke-width="{{ $strokeWidth }}"
            stroke-linecap="round"
            stroke-linejoin="round"
        />
        <path
            d="M20.9999 20.9999L16.6499 16.6499"
            stroke="{{$color}}"
            stroke-width="{{ $strokeWidth }}"
            stroke-linecap="round"
            stroke-linejoin="round"
        />
    </svg>
@endif
