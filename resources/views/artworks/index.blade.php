@extends('layouts.master')

@section('content')
    <div class="dashboard gallery mini">
        <div class="inner__table table-responsive p-2 pb-4">
            <table class="table table-striped">
                <thead>
                <tr>
                    <th scope="col">{{ __('Thumbnail') }}</th>
                    <th scope="col">{{ __('Title') }}</th>
                    <th scope="col">{{ __('Artist') }}</th>
                    <th scope="col">{{ __('Type') }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($artworks as $artwork)
                    <tr>
                        <td>
                            <div class="preview">
                                <img
                                    src="{{ $artwork->image_url }}"
                                    alt="thumbnail"
                                    width="76"
                                    height="auto"
                                />
                            </div>
                        </td>
                        <td>{{ $artwork->name }}</td>
                        <td>{{ $artwork->artist }}</td>
                        <td>{{ $artwork->type }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            {{ $artworks->links() }}
        </div>
    </div>
@endsection
