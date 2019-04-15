<div class="role-wrapper">
    <div class="col-sm-7">
        <select name="roles[{{$club->id}}][{{$role}}]" style="width:100%">
            <option value="">-- Select a User --</option>
            @foreach($club->users as $user)
                <option value="{{$user->id}}"{{in_array($user->id, $club->user_roles()->where("sub_role", "{$role}")->pluck("user_id")->toArray()) ? " selected='selected'" : ""}}>
                    {{$user->name}} <span>({{$user->email}})</span>
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-sm-5">
        {{$role_label}} Leads
    </div>
    <div style="clear:both;height:8px;"></div>
</div>
