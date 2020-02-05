@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">Leads</div>

                    <div class="panel-body">
                        <p>
                            <a class="btn btn-primary" href="{{ url('/admin/leads/create') }}">New</a>
                        </p>
                        <table class="table table-bordered table-striped table-hover datatable">
                            <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <?php /**
                                <th>Name</th>
                                <th>Division</th>
                                <th>Active</th>
                                <th>Action</th>
                                **/ ?>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($leads as $lead)
                                <tr>
                                    <td>{{ $lead->first_name }} {{ $lead->last_name }}</td>
                                    <td>{{ $lead->email }}</td>
                                    <?php /**
                                    <td>{{ $club->name }}</td>
                                    <td>{{ ucwords($club->division) }}</td>
                                    <td>{{ $club->active ? "Yes" : "No" }}</td>
                                    <td>
                                        <form class="form-inline" method="post" action="{{ url('/admin/clubs/' . $club->id) }}">
                                            {{ csrf_field() }}
                                            <input type="hidden" name="_method" value="delete" />
                                            <a class="btn btn-sm btn-info" href="{{ url('/admin/clubs/' . $club->id . '/edit') }}">edit</a>
                                            <button type="submit" class="btn btn-sm btn-danger delete" onclick="return confirm('Are you sure you wish to delete this club?');">delete</button>
                                        </form>
                                    </td>
                                    **/ ?>
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
