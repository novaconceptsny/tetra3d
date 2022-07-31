<div class="card">
    <div class="card-header d-flex flex-column">
        <div class="d-flex mb-2">
            <h5 class="me-auto">{{ $heading }}</h5>
            <div class="float-end">
                @if($routes['create'])
                    <a href="{{ route($routes['create']) }}" class="btn btn-sm btn-primary"><i class="fal fa-plus"></i> {{ __('Add New') }}</a>
                @endif
                @include('backend.includes.datatable.bulk-delete')
            </div>
        </div>
        <!-- Filters Start -->
        <div class="d-flex">
            @include('backend.includes.datatable.filters')
            @include('backend.includes.datatable.reset-filters')
        </div>
        @include('backend.includes.datatable.toggle-columns')
    </div>
    <div class="card-body p-0">
        <div class="mb-3 table-nowrap scrollbar">
            <table class="table table-hover fs--1 table-sm">

                @include('backend.includes.datatable.header')

                <tbody class="list">
                @foreach($rows as $row)
                    <tr class="dt-row">
                        @include('backend.includes.datatable.content')

                        <!-- Extended Columns -->
                        <td class="td" data-move-before="company_name">
                            <img src="{{ $row->image_url }}" alt="" width="50">
                        </td>

                        <td>
                            @include('backend.includes.datatable.actions')
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @include('backend.includes.datatable.footer')
</div>
