@foreach([
    "corporate" => "Corporate",
    "event" => "Event",
    "member" => "Membership",
    "private" => "Private Event",
    "tournament" => "Tournament",
    "wedding" => "Wedding",
] as $role => $role_label)
    @include('web.users.subviews.role')
@endforeach