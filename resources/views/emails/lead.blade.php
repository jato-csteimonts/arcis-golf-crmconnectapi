<h3>You've received a new {{ucwords($lead->sub_type)}} lead for {{$club->name ? $club->name : $lead->source}}:</h3>
<ul>
    <li><strong>First Name:</strong> {{{$lead->first_name}}}</li>
    <li><strong>Last Name:</strong> {{{$lead->last_name ?? "---"}}}</li>
    <li><strong>Email:</strong> {{{$lead->email ?? "---"}}}</li>
    <li><strong>Phone:</strong> {{{$lead->phone ?? "---"}}}</li>
    <li><strong>Source:</strong> {{{$lead->source ?? "---"}}}</li>
</ul>
<p>
    Log into <a href="https://www.reservecloud.com/web/{{$lead->sub_type == "member" ? "clubLeads/clubLeadsMain" : "leads/leadsMain"}}">Reserve Interactive</a> to view further details about this lead.
</p>