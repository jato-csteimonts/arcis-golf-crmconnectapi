You've received a new {{ucwords($lead->sub_type)}} lead for {{$club->name ? $club->name : $lead->source}}:

 - First Name: {{{$lead->first_name}}}
 - Last Name: {{{$lead->last_name ?? "---"}}}
 - Email: {{{$lead->email ?? "---"}}}
 - Phone: {{{$lead->phone ?? "---"}}}
 - Source: {{{$lead->source ?? "---"}}}