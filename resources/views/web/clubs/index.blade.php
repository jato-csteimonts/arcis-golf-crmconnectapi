@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">Cubs</div>

                    <div class="panel-body">
                        <p>
                            <a class="btn btn-primary" href="/admin/club/create">New</a>
                        </p>
                        <table class="table table-bordered table-striped table-hover datatable">
                            <thead>
                            <tr>
                                <th>Site Code</th>
                                <th>Name</th>
                                <th>Division</th>
                                <th>Active</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($clubs as $club)
                                <tr>
                                    <td>{{ $club->site_code }}</td>
                                    <td>{{ $club->name }}</td>
                                    <td>{{ ucwords($club->division) }}</td>
                                    <td>{{ $club->active ? "Yes" : "No" }}</td>
                                    <td>
                                        <form class="form-inline" method="post" action="{{ url('/admin/clubs/' . $club->id) }}">
                                            {{ csrf_field() }}
                                            <input type="hidden" name="_method" value="delete" />
                                            <a class="btn btn-sm btn-info" href="{{ url('/admin/clubs/' . $club->id . '/edit') }}">edit</a>
                                            <button type="submit" class="btn btn-sm btn-danger delete">delete</button>
                                        </form>
                                    </td>
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
