@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <a href="{{url("/home/")}}">Dashboard</a>
                        &nbsp;\&nbsp;
                        <a href="{{url("/admin/clubs/")}}">Clubs</a>
                        &nbsp;\&nbsp;
                        {{$club->id ? $club->name : "Add New Club"}}
                    </div>

                    <div class="panel-body">

                        <form action="{{ url(($club->id) ? '/admin/clubs/' . $club->id : '/admin/clubs') }}" method="post" class="form-horizontal" enctype="multipart/form-data">
                            {{ csrf_field() }}

                            @if ($club->id)
                                <input type="hidden" name="_method" value="put" />
                            @endif

                            <h3 class="text-center">Club Information</h3>

                            <div class="form-group">
                                <div class="col-sm-2">&nbsp;</div>
                                <label for="name" class="col-sm-2 control-label">Club Name</label>
                                <div class="col-sm-6">
                                    <input placeholder="Club Name" type="text" class="form-control" name="name" id="name" value="{{ $club->name }}" />
                                </div>
                                <div class="col-sm-2">&nbsp;</div>
                            </div>

                            <div class="form-group">
                                <div class="col-sm-2">&nbsp;</div>
                                <label for="division" class="col-sm-2 control-label">Division</label>
                                <div class="col-sm-6">
                                    <select name="division" id="division" style="width:100%">
                                        <option value="private"{{$club->division == "private" ? " selected='selected'" : ""}}>Private</option>
                                        <option value="public"{{$club->division == "public" ? " selected='selected'" : ""}}>Public</option>
                                    </select>
                                </div>
                                <div class="col-sm-2">&nbsp;</div>
                            </div>

                            <div class="form-group">
                                <div class="col-sm-2">&nbsp;</div>
                                <label for="site_code" class="col-sm-2 control-label">RI Site Code</label>
                                <div class="col-sm-6">
                                    <input placeholder="Reserve Site Code - Must match exactly" type="text" class="form-control" name="site_code" id="site_code" value="{{ $club->site_code }}" />
                                </div>
                                <div class="col-sm-2">&nbsp;</div>
                            </div>

                            <div class="form-group">
                                <div class="col-sm-2">&nbsp;</div>
                                <label for="active" class="col-sm-2 control-label">Active</label>
                                <div class="col-sm-6">
                                    <input type="radio" name="active" value="1" id="active-yes"{{$club->active ? " checked='checked'" : ""}} />
                                    <label for="active-yes">Yes</label>
                                    &nbsp;&nbsp;
                                    <input type="radio" name="active" value="0" id="active-no"{{!$club->active ? " checked='checked'" : ""}} />
                                    <label for="active-no">No</label>
                                </div>
                                <div class="col-sm-2">&nbsp;</div>
                            </div>

                            <h3 class="text-center">Domains</h3>
                            <div class="domains-wrapper">
                                @if(count($club->domains))
                                    @foreach($club->domains as $domain)
                                        @include('web.clubs.subviews.domain')
                                    @endforeach
                                @else
                                    @include('web.clubs.subviews.domain', ["domain" => new App\Domain()])
                                @endif
                            </div>
                            <div class="col-sm-2">&nbsp;</div>
                            <div class="col-sm-10"><a href="{{url('/admin/ajax')}}" action="edit-club-add-domain" class="add-domain">+ Add Domain</a></div>
                            <div style="clear:both;height:20px;"></div>

                            <h3 class="text-center">Users</h3>

                            <div class="col-sm-1">&nbsp;</div>
                            <div class="col-sm-4 users all">
                                <input type="text" class="form-control" name="filter" value="" placeholder="Filter by User's Name..." />
                                <select name="users-all" id="users" multiple size="13" style="width:100%">
                                    @foreach(App\User::whereNotIn("id", $club->users()->pluck("users.id")->toArray())->orderBy("name", "ASC")->get() as $user)
                                        <option value="{{$user->id}}" data-label="{{preg_replace("/([^a-z ]+)/i", "", strtolower($user->name))}}">{{$user->name}} <span>({{$user->email}})</span></option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-sm-2">
                                <button type="button" id="users_rightSelected" class="btn btn-sm btn-danger delete add-user" style="width:100%;margin:105px 0px 10px 0px;font-size:18px;">+</button>
                                <button type="button" id="users_leftSelected" class="btn btn-sm btn-danger delete remove-user" style="width:100%;font-size:18px;">-</button>
                            </div>
                            <div class="col-sm-4 users selected">
                                <input type="text" class="form-control" name="filter" value="" placeholder="Filter by User's Name..." />
                                <select name="users-selected[]" id="users_to" multiple size="13" style="width:100%">
                                    @foreach($club->users as $user)
                                        <option value="{{$user->id}}" data-label="{{preg_replace("/([^a-z ]+)/i", "", strtolower($user->name))}}">{{$user->name}} <span>({{$user->email}})</span></option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-sm-1">&nbsp;</div>
                            <div style="clear:both;height:20px;"></div>


                            <h3 class="text-center">Lead Routing</h3>
                            <div class="roles-wrapper">

                                @foreach([
                                    "corporate" => "Corporate",
                                    "event" => "Event",
                                    "member" => "Membership",
                                    "private" => "Private Event",
                                    "tournament" => "Tournament",
                                    "wedding" => "Wedding",
                                ] as $role => $role_label)
                                    @include('web.clubs.subviews.role')
                                @endforeach
                            </div>
                            <div style="clear:both;height:8px;"></div>

                            <?php /**
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
     **/ ?>

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
            $("a.add-domain").on("click", function() {
                $.ajax({
                    type: "GET",
                    url: $(this).attr("href"),
                    dataType: "json",
                    data: {
                        action: $(this).attr("action")
                    },
                    success : function(data) {
                        $("div.domains-wrapper").append(data.HTML);
                    }
                });
                return false;
            });
            $("body").on("click", "button.delete-domain", function() {
                if(confirm("Are you sure you wish to delete this domain?")) {
                    $(this).closest("div.domain-wrapper").remove();
                }
                return false;
            });
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


        });
    </script>

@endsection
