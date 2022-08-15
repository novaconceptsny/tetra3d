@extends('layouts.backend')

@section('title_right')
    <x-backend::layout.breadcrumbs>
        <x-backend::layout.breadcrumb-item text="Tours" :route="route('backend.tours.index')" />
        <x-backend::layout.breadcrumb-item text="Surfaces" :active="true"/>
    </x-backend::layout.breadcrumbs>
@endsection

@section('content')
    <livewire:datatables.artwork-datatable/>
@endsection

@section('scripts')
    <script>
        function updateColumns() {
            $(".dt-row").each(function () {
                let $row = $(this);
                $row.find('.td').each(function () {
                    let $column = $(this);
                    let move_after = $column.data('move-after');
                    let move_before = $column.data('move-before');
                    let move_to_start = $column.data('move-to-start');

                    if (move_to_start !== undefined && move_to_start === true) {
                        $column.insertBefore( $row.find(`.td:first`));
                    }

                    if (move_after !== undefined) {
                        let $moveAfterColumn = $row.find(`.td[data-column="${move_after}"]`)
                        $column.insertAfter($moveAfterColumn);
                    }

                    if (move_before !== undefined) {
                        let $moveBeforeColumn = $row.find(`.td[data-column="${move_before}"]`)
                        $column.insertBefore($moveBeforeColumn);
                    }
                })
            });
        }

        $(function () {
            updateColumns();
            document.addEventListener('contentChanged', function(e) {
                updateColumns();
            })
        })
    </script>
@endsection
