@props([
    'route' => 'javascript:void(0)',
    'permission' => null,
    'permissionParams' => null,
])

@if($permission == null || user()->can($permission, $permissionParams) )
<a {{ $attributes->merge(['class' => 'dropdown-item']) }} href="{{$route}}">{{ $slot }}</a>
@endif
