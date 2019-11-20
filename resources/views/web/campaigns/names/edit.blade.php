@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <a href="{{url("/home/")}}">Dashboard</a>
                        &nbsp;\&nbsp;
                        <a href="{{url("/admin/campaign-names/")}}">Campaign Names</a>
                        &nbsp;\&nbsp;
                        {{$campaign_name->id ? $campaign_name->name : "Add New Campaign Name"}}
                    </div>

                    <div class="panel-body">

                        <form action="{{ url(($campaign_name->id) ? '/admin/campaign-names/' . $campaign_name->id : '/admin/campaign-names') }}" method="post" class="form-horizontal" enctype="multipart/form-data">

                            {{ csrf_field() }}

                            @if ($campaign_name->id)
                                <input type="hidden" name="_method" value="put" />
                            @endif

                            <h3 class="text-center">Campaign Name Information</h3>

                            <div class="form-group">
                                <div class="col-sm-2">&nbsp;</div>
                                <label for="name" class="col-sm-2 control-label">Name</label>
                                <div class="col-sm-6">
                                    <input placeholder="Name" type="text" class="form-control" name="name" id="name" value="{{ $campaign_name->name }}" />
                                </div>
                                <div class="col-sm-2">&nbsp;</div>
                            </div>

                            <div class="form-group">
                                <div class="col-sm-2">&nbsp;</div>
                                <label for="name" class="col-sm-2 control-label">Slug</label>
                                <div class="col-sm-6">
                                    <input placeholder="Slug" type="text" class="form-control" name="slug" id="slug" value="{{ $campaign_name->slug }}" />
                                </div>
                                <div class="col-sm-2">&nbsp;</div>
                            </div>

                            <div class="form-group">
                                <div class="col-sm-2">&nbsp;</div>
                                <label for="name" class="col-sm-2 control-label">Description</label>
                                <div class="col-sm-6">
                                    <textarea name="description" class="form-control" id="description">{{ $campaign_name->description }}</textarea>
                                </div>
                                <div class="col-sm-2">&nbsp;</div>
                            </div>

                            <div class="form-group">
                                <div class="col-sm-2">&nbsp;</div>
                                <label for="name" class="col-sm-2 control-label">Club</label>
                                <div class="col-sm-6">
                                    <select name="club_id" id="club_id" class="form-control">
                                        <option value="">-- Choose a Club --</option>
                                        @foreach(\App\Club::orderBy("name", "asc")->get() as $Club)
                                            <option value="{{$Club->id}}"{{$Club->id == $campaign_name->club_id ? " selected='selected'" : ""}}>{{$Club->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-sm-2">&nbsp;</div>
                            </div>

                            <div class="form-group" style="margin-top:25px;">
                                <div class="col-sm-4">&nbsp;</div>
                                <div class="col-sm-4">
                                    <button type="submit" class="btn btn-default" style="width:100%;">Save</button>
                                </div>
                                <div class="col-sm-4">&nbsp;</div>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
