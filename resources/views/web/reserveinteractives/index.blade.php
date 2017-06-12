@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">Reserve Interactives</div>

                    <div class="panel-body">
                        <table class="table table-bordered table-striped table-hover datatable">
                            <thead>
                                <tr>
                                    <th>Lead ID</th>
                                    <th>RequestName</th>
                                    <th>Request</th>
                                    <th>Response</th>
                                    <th>Created At</th>
                                    <th>Updated At</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($reserveinteractives as $reserveinteractive)
                                    <tr>
                                        <td><a href="/admin/leads/{{ $reserveinteractive->lead_id }}">{{ $reserveinteractive->lead_id }}</a></td>
                                        <td>{{ $reserveinteractive->request_name }}</td>
                                        <td>{{ print_r(json_decode($reserveinteractive->request_json)) }}</td>
                                        <td>{{ print_r(json_decode($reserveinteractive->response)) }}</td>
                                        <td>{{ $reserveinteractive->created_at }}</td>
                                        <td>{{ $reserveinteractive->updated_at }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
