@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">Domains</div>

                    <div class="panel-body">
                        <form action="{{ url(($domain->id) ? '/admin/domains/' . $domain->id : '/admin/domains') }}" method="post" class="form-horizontal" enctype="multipart/form-data">
                            {{ csrf_field() }}

                            @if ($domain->id)
                                <input type="hidden" name="_method" value="put" />
                            @endif

                            <div class="form-group">
                                <label for="name" class="col-sm-2 control-label">Domain</label>
                                <div class="col-sm-10">
                                    <input placeholder="EXACT domain, without the http://... just mydomain.com" type="text" class="form-control" name="domain" id="name" value="{{ $domain->domain }}" />
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="owner" class="col-sm-2 control-label">Owner</label>
                                <div class="col-sm-10">
                                    <input placeholder="This should be an email address!" type="text" class="form-control" name="owner" id="owner" value="{{ $domain->owner }}" />
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="salesperson" class="col-sm-2 control-label">Salesperson</label>
                                <div class="col-sm-10">
                                    <input placeholder="This should be an email address!" type="text" class="form-control" name="salesperson" id="salesperson" value="{{ $domain->salesperson }}" />
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="club" class="col-sm-2 control-label">Club</label>
                                <div class="col-sm-10">
                                    <input placeholder="This needs to be an exact match to what is in RI." type="text" class="form-control" name="club" id="name" value="{{ $domain->club }}" />
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="division" class="col-sm-2 control-label">Division</label>
                                <div class="col-sm-10">
                                    <input placeholder="This needs to be an exact match to what is in RI." type="text" class="form-control" name="division" id="name" value="{{ $domain->division }}" />
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-sm-offset-2 col-sm-10">
                                    <button type="submit" class="btn btn-default">Save</button>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
