@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <a href="{{url("/home/")}}">Dashboard</a>
                        &nbsp;\&nbsp;
                        <a href="{{url("/admin/users/")}}">Users</a>
                        &nbsp;\&nbsp;
                        {{$user->id ? $user->name : "Add New User"}}
                    </div>

                    <div class="panel-body">

                        <form action="{{ url(($user->id) ? '/admin/users/' . $user->id : '/admin/users') }}" method="post" class="form-horizontal" enctype="multipart/form-data">
                            {{ csrf_field() }}

                            @if ($user->id)
                                <input type="hidden" name="_method" value="put" />
                            @endif

                            <h3 class="text-center">Contact Information</h3>

                            <div class="form-group">
                                <div class="col-sm-2">&nbsp;</div>
                                <label for="name" class="col-sm-2 control-label">Name</label>
                                <div class="col-sm-6">
                                    <input placeholder="Name" type="text" class="form-control" name="name" id="name" value="{{ $user->name }}" />
                                </div>
                                <div class="col-sm-2">&nbsp;</div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-2">&nbsp;</div>
                                <label for="email" class="col-sm-2 control-label">Email</label>
                                <div class="col-sm-6">
                                    <input placeholder="email@email.com" type="email" class="form-control" name="email" id="name" value="{{ $user->email }}" />
                                </div>
                                <div class="col-sm-2">&nbsp;</div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-2">&nbsp;</div>
                                <label for="password" class="col-sm-2 control-label">Password</label>
                                <div class="col-sm-6">
                                    <input placeholder="********" type="password" class="form-control" name="password" id="password" value="" />
                                </div>
                                <div class="col-sm-2">&nbsp;</div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-2">&nbsp;</div>
                                <label for="is_admin" class="col-sm-2 control-label">Is Admin</label>
                                <div class="col-sm-6">
                                    <input type="radio" name="is_admin" value="1" id="is_admin-yes"{{$user->is_admin ? " checked='checked'" : ""}} />
                                    <label for="is_admin-yes">Yes</label>
                                    &nbsp;&nbsp;
                                    <input type="radio" name="is_admin" value="0" id="is_admin-no"{{!$user->is_admin ? " checked='checked'" : ""}} />
                                    <label for="is_admin-no">No</label>
                                </div>
                                <div class="col-sm-2">&nbsp;</div>
                            </div>

                            <h3 class="text-center">Clubs</h3>

                            <div class="col-sm-1">&nbsp;</div>
                            <div class="col-sm-4 users all">
                                <input type="text" class="form-control" name="filter" value="" placeholder="Filter by Club's Name..." />
                                <select name="clubs-all" id="users" multiple size="13" style="width:100%">
                                    @foreach(App\Club::whereNotIn("id", $user->clubs()->pluck("clubs.id")->toArray())->orderBy("name", "ASC")->get() as $club)
                                        <option value="{{$club->id}}" data-label="{{preg_replace("/([^a-z ]+)/i", "", strtolower($club->name))}}">{{$club->name}} <span>({{$club->division}})</span></option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-sm-2">
                                <button type="button" id="users_rightSelected" class="btn btn-sm btn-danger delete add-user" style="width:100%;margin:105px 0px 10px 0px;font-size:18px;">+</button>
                                <button type="button" id="users_leftSelected" class="btn btn-sm btn-danger delete remove-user" style="width:100%;font-size:18px;">-</button>
                            </div>
                            <div class="col-sm-4 users selected">
                                <input type="text" class="form-control" name="filter" value="" placeholder="Filter by Club's Name..." />
                                <select name="clubs-selected[]" id="users_to" multiple size="13" style="width:100%">
                                    @foreach($user->clubs as $club)
                                        <option value="{{$club->id}}" data-label="{{preg_replace("/([^a-z ]+)/i", "", strtolower($club->name))}}">
                                            {{$club->name}} <span>({{$club->division}})</span>
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-sm-1">&nbsp;</div>
                            <div style="clear:both;height:20px;"></div>

                            <h3 class="text-center">Lead Routing</h3>
                            <select name="lead-routing-club" id="lead-routing-club" style="display:block;width:50%;margin:0px auto;">
                                @foreach($user->clubs as $club)
                                    <option value="{{$club->id}}" data-label="{{preg_replace("/([^a-z ]+)/i", "", strtolower($club->name))}}"{{Session::get('roles_club_id') == $club->id ? " selected='selected'" : ""}}>
                                        {{$club->name}} <span>({{$club->division}})</span>
                                    </option>
                                @endforeach
                            </select>
                            <div class="roles-wrapper" style="margin:15px 15% 15px 15%;padding-top:15px;border-top:1px dashed #ccc;"></div>
                            <div style="clear:both;height:8px;"></div>

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

    <script>
        $(document).ready(function() {
            $("input[name='filter']").typeWatch({
                callback: function(value) {
                    var obj = $(this).closest("div.users");
                    value = value.toLowerCase();
                    //console.log(value);
                    switch(true) {
                        case value.length === 0:
                        case $( 'select option[data-label*="'+value+'"]', obj ).length === 0:
                            $( "select option", obj ).show();
                            break;
                        default:
                            $( "select option", obj ).hide();
                            $( 'select option[data-label*="'+value+'"]', obj ).show();
                            break;
                    }
                },
                wait: 0,
                highlight: false,
                allowSubmit: false,
                captureLength: 0
            });

            $('#users').multiselect();

            $("#lead-routing-club").on("change", function() {

                $.ajax({
                    type: "GET",
                    url: "{{url('/admin/ajax')}}",
                    dataType: "json",
                    data: {
                        action: "edit-user-get-roles",
                        club_id: $(this).val(),
                        user_id: "{{$user->id}}"
                    },
                    success : function(data) {
                        $("div.roles-wrapper").html(data.HTML);
                    }
                });
                return false;

            });

            $("#lead-routing-club").trigger("change");

        });
    </script>

@endsection
