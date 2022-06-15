@props([
    'route' => '#',
    'method' => 'put',
    'permission' => null,
    'permissionParams' => null,
])

@if($permission == null || user()->can($permission, $permissionParams) )
<form action="{{ $route }}" method="post">
    @csrf
    @method($method)
    {{ $slot }}
</form>
@endif
