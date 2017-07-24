@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">Fields</div>

                    <div class="panel-body">
                        <form action="{{ url(($field->id) ? '/admin/fields/' . $field->id : '/admin/fields') }}" method="post" class="form-horizontal" enctype="multipart/form-data">
                            {{ csrf_field() }}

                            @if ($field->id)
                                <input type="hidden" name="_method" value="put" />
                            @endif

                            <div class="form-group">
                                <label for="name" class="col-sm-2 control-label">From</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" name="from" id="to" value="{{ $field->from }}" />
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="name" class="col-sm-2 control-label">To</label>
                                <div class="col-sm-10">
                                        <select name="to" id="to" class="form-control">
                                            @foreach($ri_fields as $ri_main_group_label => $ri_field_sub_groups)
                                                <optgroup label="{{ $ri_main_group_label }}">
                                                @foreach ($ri_field_sub_groups as $ri_sub_group_label => $ri_field_sub_sub_groups)
                                                    <optgroup label="--&gt; {{ $ri_sub_group_label }}">
                                                        @foreach ($ri_field_sub_sub_groups as $ri_field)
                                                        <option value="{{ $ri_field }}" {{ ($ri_field == $field->to) ? 'selected' : '' }} >{{ $ri_field }}</option>
                                                        @endforeach
                                                    </optgroup>
                                                @endforeach
                                                </optgroup>
                                            @endforeach
                                        </select>
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
