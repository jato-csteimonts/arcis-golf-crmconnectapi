@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">Leads</div>

                    <div class="panel-body">
                        <p>
                            <a class="btn btn-primary" href="/users/create">New</a>
                        </p>
                        <table class="table table-bordered table-striped table-hover datatable">
                            <thead>
                                <tr>
                                    <th>Source</th>
                                    <th>Raw Data</th>
                                    <th>Created At</th>
                                    <th>Updated At</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($leads as $lead)
                                    <tr>
                                        <td>{{ $lead->source }}</td>
                                        <td><?php print_r(unserialize($lead->raw)) ?></td>
                                        <td>{{ $lead->created_at }}</td>
                                        <td>{{ $lead->updated_at }}</td>
                                        <td>
                                            <form class="form-inline" method="post" action="{{ url('/users/' . $lead->id) }}">
                                                {{ csrf_field() }}
                                                <input type="hidden" name="_method" value="delete" />
                                                <a class="btn btn-sm btn-info" href="{{ url('/users/' . $lead->id . '/edit') }}">edit</a>
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
