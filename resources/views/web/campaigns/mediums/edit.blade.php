@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <a href="{{url("/home/")}}">Dashboard</a>
                        &nbsp;\&nbsp;
                        <a href="{{url("/admin/campaign-mediums/")}}">Campaign Mediums</a>
                        &nbsp;\&nbsp;
                        {{$campaign_medium->id ? $campaign_medium->name : "Add New Campaign Medium"}}
                    </div>

                    <div class="panel-body">

                        <form action="{{ url(($campaign_medium->id) ? '/admin/campaign-mediums/' . $campaign_medium->id : '/admin/campaign-mediums') }}" method="post" class="form-horizontal" enctype="multipart/form-data">

                            {{ csrf_field() }}

                            @if ($campaign_medium->id)
                                <input type="hidden" name="_method" value="put" />
                            @endif

                            <h3 class="text-center">Campaign Medium Information</h3>

                            <div class="form-group">
                                <div class="col-sm-2">&nbsp;</div>
                                <label for="name" class="col-sm-2 control-label">Name</label>
                                <div class="col-sm-6">
                                    <input placeholder="Campaign Medium Name" type="text" class="form-control" name="name" id="name" value="{{ $campaign_medium->name }}" />
                                </div>
                                <div class="col-sm-2">&nbsp;</div>
                            </div>

                            <div class="form-group">
                                <div class="col-sm-2">&nbsp;</div>
                                <label for="name" class="col-sm-2 control-label">Slug</label>
                                <div class="col-sm-6">
                                    <input placeholder="Campaign Medium Slug" type="text" class="form-control" name="slug" id="slug" value="{{ $campaign_medium->slug }}" />
                                </div>
                                <div class="col-sm-2">&nbsp;</div>
                            </div>

                            <div class="form-group">
                                <div class="col-sm-2">&nbsp;</div>
                                <label for="name" class="col-sm-2 control-label">Code</label>
                                <div class="col-sm-6">
                                    <input placeholder="Campaign Medium Code" type="text" class="form-control" name="code" id="code" value="{{ $campaign_medium->code }}" />
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
