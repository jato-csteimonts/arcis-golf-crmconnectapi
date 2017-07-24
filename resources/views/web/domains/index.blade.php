@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">Domains</div>

                    <div class="panel-body">
                        <p>
                            <a class="btn btn-primary" href="/admin/domains/create">New</a>
                        </p>
                        <table class="table table-bordered table-striped table-hover datatable">
                            <thead>
                                <tr>
                                    <th>Domain</th>
                                    <th>Owner</th>
                                    <th>Salesperson</th>
                                    <th>Club</th>
                                    <th>Division</th>
                                    <th>Created At</th>
                                    <th>Updated At</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($domains as $domain)
                                    <tr>
                                        <td>{{ $domain->domain }}</td>
                                        <td>{{ $domain->owner }}</td>
                                        <td>{{ $domain->salesperson }}</td>
                                        <td>{{ $domain->club }}</td>
                                        <td>{{ $domain->division }}</td>
                                        <td>{{ $domain->created_at }}</td>
                                        <td>{{ $domain->updated_at }}</td>
                                        <td>
                                            <form class="form-inline" method="post" action="{{ url('/admin/domains/' . $domain->id) }}">
                                                {{ csrf_field() }}
                                                <input type="hidden" name="_method" value="delete" />
                                                <a class="btn btn-sm btn-info" href="{{ url('/admin/domains/' . $domain->id . '/edit') }}">edit</a>
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
