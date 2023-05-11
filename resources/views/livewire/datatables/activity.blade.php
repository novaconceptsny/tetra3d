<div class="card">
    <x-loader/>
    <div class="card-header d-flex flex-column">
        <div class="d-flex mb-2">
            <h5 class="me-auto">{{ $heading }}</h5>
            <div class="float-end">
                @if(isset($routes['create']))
                    <a href="{{ route($routes['create']) }}" class="btn btn-sm btn-primary"><i
                            class="fal fa-plus"></i> {{ __('Add New') }}</a>
                @endif
                @include('backend.includes.datatable.bulk-delete')
            </div>
        </div>
        <!-- Filters Start -->
        <div class="d-flex">
            @include('backend.includes.datatable.search')
            <div class="me-1">
                <select wire:model="selectedProject" class="form-control">
                    <option value="">All Projects</option>
                    @foreach($projects as $project)
                        <option value="{{$project->id}}">{{$project->name}}</option>
                    @endforeach
                </select>
            </div>

            <div class="me-1">
                <select wire:model="selectedTour" class="form-control">
                    <option value="">All Tours</option>
                    @foreach($tours as $tour)
                        <option value="{{$tour->id}}">{{$tour->name}}</option>
                    @endforeach
                </select>
            </div>
            @include('backend.includes.datatable.reset-filters')
        </div>

        @include('backend.includes.datatable.toggle-columns')
    </div>
    <div class="card-body py-0">
        <div class="mb-3 scrollbar table-responsive">
            <table class="table table-hover fs--1 table-sm">

                @include('backend.includes.datatable.header')

                <tbody class="list">
                @foreach($rows as $row)
                    <tr class="dt-row">
                        @include('backend.includes.datatable.bulk-selection')

                        @include('backend.includes.datatable.content')

                        <td>
                            @if($row->tour_id)
                            <a href="{{ route('tours.show', ['tour' => $row->tour_id, 'project_id' => $row->project_id]) }}" class="text-decoration-none">View</a>
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @include('backend.includes.datatable.footer')
</div>
