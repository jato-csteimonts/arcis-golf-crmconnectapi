<div class="domain-wrapper">
    <div class="col-sm-2">&nbsp;</div>
    <div class="col-sm-6">
        <input type="text" placeholder="{{$domain->id ? "" : "Domain without 'http(s)' and 'www'"}}" class="form-control" name="domains{{$domain->id ? "[existing][{$domain->id}]" : "[new][]"}}" value="{{$domain->domain ?? ""}}" />
    </div>
    <div class="col-sm-2">
        <button type="button" class="btn btn-sm btn-danger delete delete-domain" style="width:100%;">delete</button>
    </div>
    <div class="col-sm-2">&nbsp;</div>
    <div style="clear:both;height:8px;"></div>
</div>
