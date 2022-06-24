@props([
    'errors',
    'message' => 'There are errors in your form, please check for the errors'
])

@if ($errors->any())
    <div class="alert alert-danger">
        <i class="fal fa-exclamation-circle"></i>
        <span>{{ $message }}</span>
    </div>
@endif
