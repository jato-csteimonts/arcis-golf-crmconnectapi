@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <a href="{{url("/home/")}}">Dashboard</a>
                        &nbsp;\&nbsp;
                        <a href="{{url("/admin/campaign-terms/")}}">Campaign Terms</a>
                        &nbsp;\&nbsp;
                        {{$campaign_term->id ? $campaign_term->name : "Add New Campaign Term"}}
                    </div>

                    <div class="panel-body">

                        <form action="{{ url(($campaign_term->id) ? '/admin/campaign-terms/' . $campaign_term->id : '/admin/campaign-terms') }}" method="post" class="form-horizontal" enctype="multipart/form-data">

                            {{ csrf_field() }}

                            @if ($campaign_term->id)
                                <input type="hidden" name="_method" value="put" />
                            @endif

                            <h3 class="text-center">Campaign Term Information</h3>

                            <div class="form-group">
                                <div class="col-sm-2">&nbsp;</div>
                                <label for="name" class="col-sm-2 control-label">Name</label>
                                <div class="col-sm-6">
                                    <input placeholder="Name" type="text" class="form-control" name="name" id="name" value="{{ $campaign_term->name }}" />
                                </div>
                                <div class="col-sm-2">&nbsp;</div>
                            </div>

                            <div class="form-group">
                                <div class="col-sm-2">&nbsp;</div>
                                <label for="name" class="col-sm-2 control-label">Slug</label>
                                <div class="col-sm-6">
                                    <input placeholder="Slug" type="text" class="form-control" name="slug" id="slug" value="{{ $campaign_term->slug }}" />
                                </div>
                                <div class="col-sm-2">&nbsp;</div>
                            </div>

                            <div class="form-group">
                                <div class="col-sm-2">&nbsp;</div>
                                <label for="name" class="col-sm-2 control-label">Code</label>
                                <div class="col-sm-6">
                                    <input placeholder="Campaign Term Code" type="text" class="form-control" name="code" id="code" value="{{ $campaign_term->code }}" />
                                </div>
                                <div class="col-sm-2">&nbsp;</div>
                            </div>

                            <div class="form-group">
                                <div class="col-sm-2">&nbsp;</div>
                                <label for="name" class="col-sm-2 control-label">Description</label>
                                <div class="col-sm-6">
                                    <textarea name="description" class="form-control" id="description">{{ $campaign_term->description }}</textarea>
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
