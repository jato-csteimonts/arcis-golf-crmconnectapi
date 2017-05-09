@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">Unbounces</div>

                    <div class="panel-body">
                        <form action="{{ url(($unbounce->id) ? '/leads/' . $unbounce->id : '/leads') }}" method="post" class="form-horizontal" enctype="multipart/form-data">
                            {{ csrf_field() }}

                            @if ($unbounce->id)
                                <input type="hidden" name="_method" value="put" />
                            @endif

                            <div class="form-group">
                                <label for="name" class="col-sm-2 control-label">Name</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" name="name" id="name" value="{{ $unbounce->name }}" />
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="email" class="col-sm-2 control-label">Email</label>
                                <div class="col-sm-10">
                                    <input type="email" class="form-control" name="email" id="email" value="{{ $unbounce->email }}" />
                                </div>
                            </div>


                            <div class="form-group">
                                <div class="col-sm-2"></div>
                                <div class="col-sm-10">
                                    @if ($unbounce->id)
                                    Passwords can be set via the forgot password script on the login form. To change the lead's password, instruct the lead to visit the login page and follow the forgot password procedure.
                                    @else
                                    After creating a new lead, please instruct the lead to visit the login page and follow the forgot password procedure to set their password for the first time.
                                    @endif
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
