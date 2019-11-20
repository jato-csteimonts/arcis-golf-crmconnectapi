@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">Campaign Names</div>

                    <div class="panel-body">
                        <p>
                            <a class="btn btn-primary" href="{{ url('/admin/campaign-names/create') }}">New</a>
                        </p>
                        <table class="table table-bordered table-striped table-hover datatable">
                            <thead>
                            <tr>
                                <th>Name</th>
                                <th>Slug</th>
                                <th>Club</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($campaign_names as $campaign_name)
                                <tr>
                                    <td>{{ $campaign_name->name }}</td>
                                    <td>{{ $campaign_name->slug }}</td>
                                    <td>{{ $campaign_name->club_id ? \App\Club::find($campaign_name->club_id)->name : "---" }}</td>
                                    <td>
                                        <form class="form-inline" method="post" action="{{ url('/admin/campaign-names/' . $campaign_name->id) }}">
                                            {{ csrf_field() }}
                                            <input type="hidden" name="_method" value="delete" />
                                            <a class="btn btn-sm btn-info" href="{{ url('/admin/campaign-names/' . $campaign_name->id . '/edit') }}">edit</a>
                                            <button type="submit" class="btn btn-sm btn-danger delete" onclick="return confirm('Are you sure you wish to delete this campaign name?');">delete</button>
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
