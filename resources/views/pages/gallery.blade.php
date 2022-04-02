@extends('layouts.master')

@section('content')
    <div class="dashboard gallery mini">
        <div class="inner__table table-responsive p-2 pb-4">
            <table class="table table-striped">
                <thead>
                <tr>
                    <th scope="col">Thumnail</th>
                    <th scope="col">Company Name</th>
                    <th scope="col">Person Name</th>
                    <th scope="col">Website</th>
                    <th scope="col">stage</th>
                    <th scope="col">Founded</th>
                    <th scope="col">Founded</th>
                    <th scope="col">Founded</th>
                    <th scope="col">Founded</th>
                </tr>
                </thead>
                <tbody>
                @for($i=0; $i<25; $i++)
                    <tr>
                        <td>
                            <div class="preview">
                                <img
                                    src="{{ asset('images/thumb.png') }}"
                                    alt="thumbnail"
                                    width="76"
                                    height="auto"
                                />
                            </div>
                        </td>
                        <td>Giskard Datatech Pvt Ltd</td>
                        <td>Amber Pabreja</td>
                        <td>www.trendlyne.com</td>
                        <td>Series A</td>
                        <td>2016</td>
                        <td>Series A</td>
                        <td>2016</td>
                        <td>2016</td>
                    </tr>
                @endfor
                </tbody>
            </table>
            <div class="footer__pagination mini">
                <p class="pagination__text">Showing 1 to 10 enteries</p>
            </div>
        </div>
    </div>
@endsection
