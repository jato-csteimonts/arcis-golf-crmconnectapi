@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">Fields</div>

                    <div class="panel-body">
                        <p>
                            <a class="btn btn-primary" href="/admin/fields/create">New</a>
                        </p>
                        <table class="table table-bordered table-striped table-hover datatable">
                            <thead>
                                <tr>
                                    <th>From</th>
                                    <th>To</th>
                                    <th>Created At</th>
                                    <th>Updated At</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($fields as $field)
                                    <tr>
                                        <td>{{ $field->from }}</td>
                                        <td>{{ $field->to }}</td>
                                        <td>{{ $field->created_at }}</td>
                                        <td>{{ $field->updated_at }}</td>
                                        <td>
                                            <form class="form-inline" method="post" action="{{ url('/admin/fields/' . $field->id) }}">
                                                {{ csrf_field() }}
                                                <input type="hidden" name="_method" value="delete" />
                                                <a class="btn btn-sm btn-info" href="{{ url('/admin/fields/' . $field->id . '/edit') }}">edit</a>
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
