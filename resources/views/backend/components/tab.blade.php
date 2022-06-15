@props([
    'id' => 'tab',
    'bordered' => true,
    'card' => false,
    'justified' => false,
    'tabClasses' => ''
])

@php
    $tabClasses .= $bordered ? ' nav-bordered' : '';
    $tabClasses .= $justified ? ' nav-justified' : '';
@endphp

@if($card)
    <div class="card">
        <div class="card-header border-0 px-0 pb-0">
            @endif
            <ul class="nav nav-tabs {{ $tabClasses }}" id="{{$id}}" role="tablist">
                {{ $tabs }}
            </ul>
        </div>
        @if($card)
            <div class="card-body p-0">
                @endif
                <div class="tab-content {{ $card ? '' : 'p-3' }}" id="{{$id}}Content">
                    {{ $slot }}
                </div>
                @if($card)
            </div>
    </div>
@endif

