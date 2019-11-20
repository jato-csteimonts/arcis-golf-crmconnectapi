@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">Campaign Terms</div>

                    <div class="panel-body">
                        <p>
                            <a class="btn btn-primary" href="{{ url('/admin/campaign-terms/create') }}">New</a>
                        </p>
                        <table class="table table-bordered table-striped table-hover datatable">
                            <thead>
                            <tr>
                                <th>Code</th>
                                <th>Name</th>
                                <th>Slug</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($campaign_terms as $campaign_term)
                                <tr>
                                    <td>{{ $campaign_term->code }}</td>
                                    <td>{{ $campaign_term->name }}</td>
                                    <td>{{ $campaign_term->slug }}</td>
                                    <td>
                                        <form class="form-inline" method="post" action="{{ url('/admin/campaign-terms/' . $campaign_term->id) }}">
                                            {{ csrf_field() }}
                                            <input type="hidden" name="_method" value="delete" />
                                            <a class="btn btn-sm btn-info" href="{{ url('/admin/campaign-terms/' . $campaign_term->id . '/edit') }}">edit</a>
                                            <button type="submit" class="btn btn-sm btn-danger delete" onclick="return confirm('Are you sure you wish to delete this campaign term?');">delete</button>
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
