<section class="collection">
    <div class="container-fluid main-intro">
        <div class="row table-row">

            <!-- <h5>You’r Collection</h5> -->
            <div class="c-form">
                <div class="form-group custum=group">
                    <input class="form-control" type="search" placeholder="Search" />
                </div>
                <div class="dropdown custum-group">
                    <button
                        class="btn btn-secondary dropdown"
                        type="button"
                        id="dropdownMenuButton1"
                        data-bs-toggle="dropdown"
                        aria-expanded="false"
                    >
                        All collections
                    </button>
                    <ul
                        class="dropdown-menu w-100"
                        aria-labelledby="dropdownMenuButton1"
                    >
                        <li><a class="dropdown-item" href="#">Action</a></li>
                        <li><a class="dropdown-item" href="#">Another action</a></li>
                        <li>
                            <a class="dropdown-item" href="#">Something else here</a>
                        </li>
                    </ul>
                </div>

                <div class="dropdown custum-group">
                    <button
                        class="btn btn-secondary dropdown"
                        type="button"
                        id="dropdownMenuButton1"
                        data-bs-toggle="dropdown"
                        aria-expanded="false"
                    >
                        All Companies
                    </button>
                    <ul
                        class="dropdown-menu w-100"
                        aria-labelledby="dropdownMenuButton1"
                    >
                        <li><a class="dropdown-item" href="#">Action</a></li>
                        <li><a class="dropdown-item" href="#">Another action</a></li>
                        <li>
                            <a class="dropdown-item" href="#">Something else here</a>
                        </li>
                    </ul>
                </div>
                <div class="col custum-group">
                    <button class="form-control reset-btn" type="reset">Reset</button>
                </div>
            </div>
            <!-- </div> -->
            <table class="w-100  collection-table table table-bordered">
                <thead>
                <tr>
                    <th style="width: 4%"></th>
                    <th>IMAGE</th>
                    <th style="width: 20%">Company</th>
                    <th>Collection</th>
                    <th>Name</th>
                    <th>Dimension H x W</th>
                    <th style="width: 20%">Artist</th>
                    <th>Type</th>
                </tr>
                </thead>
                <tbody>
                @foreach($rows as $row)
                    <tr class="dt-row">
                        @include('backend.includes.datatable.bulk-selection')
                        <td>
                            <img src="{{ $row->image_url }}" alt="" width="50">
                        </td>

                        @foreach($columns as $col_name => $column)
                            @if($column['visible'] && ($column['render'] ?? true))
                                <td class="" data-column="{{ $col_name }}" >{!! $row->$col_name !!}</td>
                            @endif
                        @endforeach
                    </tr>
                @endforeach
                </tbody>
            </table>
            <div class="pagination-div pagination-white">
                {{ $rows->appends(request()->query())->links() }}
            </div>

        </div>
    </div>
</section>
