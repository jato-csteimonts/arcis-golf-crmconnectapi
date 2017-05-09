@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">Unbounces</div>

                    <div class="panel-body">
                        <p>
                            <a class="btn btn-primary" href="/users/create">New</a>
                        </p>
                        <table class="table table-bordered table-striped table-hover datatable">
                            <thead>
                                <tr>
                                    <th>Gen. Info</th>
                                    <th>Div/Club</th>
                                    <th>Owner</th>
                                    <th>Salesperson</th>
                                    <th>Notes</th>
                                    <th>Page Info</th>
                                    <th>UB Date/Time</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($unbounces as $unbounce)
                                    <tr>
                                        <td>{{ $unbounce->name }}<br />
                                            {{ $unbounce->telephone }}<br />
                                            <a href="mailto:{{ $unbounce->email }}">{{ $unbounce->email }}</a></td>
                                        <td><b>Div: </b>{{ $unbounce->division }}<br />
                                            <b>Club: </b>{{ $unbounce->club }}</td>
                                        <td><a href="mailto:{{ $unbounce->owner }}">{{ $unbounce->owner }}</a></td>
                                        <td><a href="mailto:{{ $unbounce->salesperson }}">{{ $unbounce->salesperson }}</a></td>
                                        <td>{{ $unbounce->notes }}</td>
                                        <td><b>IP:</b> {{ $unbounce->ip_address }}<br />
                                            <b>UUID:</b> {{ $unbounce->page_uuid }}<br />
                                            <b>Variant:</b> {{ $unbounce->variant }}<br />
                                            <b>URL:</b> <a href="{{ $unbounce->page_url }}" target="_blank">{{ $unbounce->page_url }}</a><br />
                                            <b>Name:</b> {{ $unbounce->page_name }}</td>
                                        <td>{{ $unbounce->date_submitted }} {{ $unbounce->time_submitted }}</td>
                                        <td>
                                            <form class="form-inline" method="post" action="{{ url('/users/' . $unbounce->id) }}">
                                                {{ csrf_field() }}
                                                <input type="hidden" name="_method" value="delete" />
                                                <a class="btn btn-sm btn-info" href="{{ url('/users/' . $unbounce->id . '/edit') }}">edit</a>
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
