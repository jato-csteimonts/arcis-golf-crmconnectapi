
@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">Lead Report</div>

                    <div class="panel-body">
                        @foreach($clubs as $club)
                            <hr />
                            <h3>{{ $club->title }}</h3>
                            <hr />
                            <div class="row">
                                <div class="col-sm-3">
                                    <h4>Total Leads</h4>
                                    <ul>
                                        <li>
                                            <b>Digital Leads: </b><br />
                                            Total: {{ $club->digitalLeads->count() }} | Converted: {{ $club->digitalLeadsConverted }}
                                        </li>
                                        <li>
                                            <b>Website Leads: </b><br />
                                            Total: {{ $club->websiteLeads->count() }} | Converted: {{ $club->websiteLeadsConverted }}
                                        </li>
                                        <li>
                                            <b>Total Leads: </b><br />
                                            Total: {{ $club->digitalLeads->count() + $club->websiteLeads->count() }} | Converted: {{ $club->websiteLeadsConverted + $club->digitalLeadsConverted }}
                                        </li>
                                    </ul>
                                </div>
                                <div class="col-sm-4">
                                    <h4>Converted Leads by Member Type</h4>
                                    <ul>
                                        @foreach ($club->member_types as $member_type)
                                            <li><b>{{ $member_type->member_type }}: </b>{{ $member_type->total }}</li>
                                        @endforeach
                                    </ul>


                                </div>
                                <div class="col-sm-4">
                                    <h4>Dues</h4>
                                    <ul>
                                        <li><b>Monthly Dues Added: </b> ${{ number_format($club->total_monthly_dues, 2) }}</li>
                                        <li><b>x12: </b> ${{ number_format($club->total_monthly_dues * 12, 2) }}</li>
                                        <li><b>Lifetime: </b> ${{ number_format($club->total_monthly_dues * 84, 2) }}</li>
                                    </ul>
                                </div>
                            </div>

                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
