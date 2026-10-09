@extends('layouts.redesign')

{{-- Sculptures of the user's company, managed from Inventory (company admins + super admins).
     Controller: App\Http\Controllers\InventorySculptureController --}}

@section('content')
<section class="collection">
    <div class="container-fluid py-4 px-4">

        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
            <div class="d-flex align-items-center gap-3">
                <a href="{{ route('inventory.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left me-1"></i> Inventory
                </a>
                <h5 class="mb-0">Sculptures</h5>
            </div>

            <div class="d-flex align-items-center gap-2">
                <form method="GET" action="{{ route('inventory.sculptures.index') }}" class="d-flex align-items-center gap-2">
                    <select name="collection_id" class="form-select form-select-sm" onchange="this.form.submit()" style="min-width: 200px;">
                        <option value="">All collections</option>
                        @foreach($collections as $collection)
                            <option value="{{ $collection->id }}" @selected((string) $collectionId === (string) $collection->id)>
                                {{ $collection->name }}
                            </option>
                        @endforeach
                    </select>
                </form>
                <a href="{{ route('inventory.sculptures.create') }}" class="btn btn-sm text-white" style="background: #099F9A;">
                    <i class="fas fa-plus me-1"></i> Add Sculpture
                </a>
            </div>
        </div>

        @if($errors->any())
            <div class="alert alert-danger">{{ $errors->first() }}</div>
        @endif

        <div class="table-responsive">
            <table class="table table-striped table-bordered align-middle" style="width: 100%">
                <thead style="background: #00474f; color: #fff;">
                    <tr>
                        <th style="width: 90px;">Image</th>
                        @if($showCompany)
                            <th>Company</th>
                        @endif
                        <th>Collection</th>
                        <th>Name</th>
                        <th>Artist</th>
                        <th>Type</th>
                        <th>Size (L x W x H, m)</th>
                        <th style="width: 110px;"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sculptures as $sculpture)
                        <tr>
                            <td>
                                @if($thumb = $sculpture->getFirstMediaUrl('thumbnail'))
                                    <img src="{{ $thumb }}" alt="{{ $sculpture->name }}" style="width: 70px; height: 70px; object-fit: cover; border-radius: 6px;">
                                @else
                                    <div class="text-muted small">No image</div>
                                @endif
                            </td>
                            @if($showCompany)
                                <td>{{ $sculpture->company->name ?? '-' }}</td>
                            @endif
                            <td>{{ $sculpture->collection->name ?? '-' }}</td>
                            <td>{{ $sculpture->name }}</td>
                            <td>{{ $sculpture->artist }}</td>
                            <td>{{ $sculpture->type }}</td>
                            <td>
                                {{ number_format((float) ($sculpture->data->length ?? 0), 2) }}
                                x {{ number_format((float) ($sculpture->data->width ?? 0), 2) }}
                                x {{ number_format((float) ($sculpture->data->height ?? 0), 2) }}
                                @if(is_numeric($sculpture->data->scale ?? null) && abs($sculpture->data->scale - 1) > 0.0001)
                                    <div class="text-muted small">{{ round($sculpture->data->scale * 100, 1) }}% of model</div>
                                @endif
                            </td>
                            <td class="text-nowrap">
                                <a href="{{ route('inventory.sculptures.edit', $sculpture) }}" class="btn btn-sm btn-outline-secondary" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form method="POST" action="{{ route('inventory.sculptures.destroy', $sculpture) }}" class="d-inline"
                                      onsubmit="return confirm('Delete sculpture &quot;{{ addslashes($sculpture->name) }}&quot;? It will also disappear from every layout where it is placed.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $showCompany ? 8 : 7 }}" class="text-center text-muted py-4">
                                No sculptures yet. Click "Add Sculpture" to upload one.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</section>
@endsection
