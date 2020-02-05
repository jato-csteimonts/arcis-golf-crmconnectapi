<?php

chdir(__DIR__);
require __DIR__ . '/../bootstrap/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
use Illuminate\Contracts\Console\Kernel;
$app->make(Kernel::class)->bootstrap();

$ServiceProvider = new \App\ServiceProviders\ReserveInteractive();

$headers = [
	"lead_id",
	"club_id",
	"club_name",
	"lead_first_name",
	"lead_last_name",
	"lead_email",
    "lead_source",
	"medium",
	"revenue_category",
	"campaign_term",
	"campaign_name",
	"status",
    "last_activity",
	"next_action",
	"converted",
	"assigned_to_name",
	"assigned_to_email",
	"reserve_error",
	"created_at"
];

$Leads = [];

$AllLeads = App\Leads\Base::whereNull("duplicate_of")
                          ->where("created_at", ">=", "2020-01-01T00:00:00")
                          //->where("created_at", ">=", "2019-10-31T00:00:00")
                          //->where("created_at",  "<", "2020-01-01T00:00:00")
                          //->where("sub_type", "!=", "member")
                          //->where("sub_type", "=", "member")
                          //->where("club_id", 14)
                          //->whereIn("campaign_term_id", [1,4])
                          ->take(1)
                          ->orderBy("created_at", "DESC");

$AllCount = $AllLeads->count();

foreach($AllLeads->get() as $index => $Lead) {

	//print_r($Lead->toArray());

	print(($index+1) . " of {$AllCount} (Lead ID: {$Lead->id})...\n");

	try {
		\App\Club::findOrFail($Lead->club_id);
	} catch (\Exception $e) {
		print(" - ERROR: Could not find Club ID '{$Lead->club_id}'...\n");
		continue;
	}

	$CurrentLead = [];
	$CurrentLead['lead_id'] = $Lead->id;
	$CurrentLead['club_id'] = \App\Club::find($Lead->club_id)->site_code;
	$CurrentLead['club_name'] = \App\Club::find($Lead->club_id)->name;
    $CurrentLead['lead_source'] = "Digital";
	$CurrentLead['lead_first_name'] = $Lead->first_name;
	$CurrentLead['lead_last_name'] = $Lead->last_name;
	$CurrentLead['lead_email'] = $Lead->email;
	$CurrentLead['medium'] = $Lead->campaign_medium_id ? \App\CampaignMedium::find($Lead->campaign_medium_id)->slug : "";
	$CurrentLead['campaign_term'] = $Lead->campaign_term_id ? \App\CampaignTerm::find($Lead->campaign_term_id)->slug : "";
	$CurrentLead['campaign_name'] = $Lead->campaign_name_id ? \App\CampaignName::find($Lead->campaign_name_id)->slug : "";

	switch($Lead->revenue_category) {
		case 1: $revenue_category = "membership"; break;
		case 2: $revenue_category = "wedding"; break;
		case 3: $revenue_category = "private"; break;
	}
	$CurrentLead['revenue_category'] = $revenue_category;



    $data = unserialize($Lead->data);

    //print_r($data);
    //print_r($Lead->toArray());

    if($Lead->ri_id){
        switch($Lead->sub_type) {
            case "member":
                $request    = "tap_clicks_member_leads";
                $lead_type  = "member";
                break;
            default:
                $request    = "tap_clicks_event_leads";
                $lead_type  = "event";
                break;
        }

        $filters = [
            "'uniqueId','EQUAL_TO','{$Lead->ri_id}'"
        ];

        $lead_type = preg_match("/_member_/", $request) ? "member" : "event";

        $args = [
            'auth' => [
                env('RESERVE_INTERACTIVE_USERNAME'),
                env('RESERVE_INTERACTIVE_PASSWORD')
            ],
            'query' => [
                'requestName'  => $request,
                'requestGuid'  => md5(date('YmdHis')),
                'maxResults'   => 1,
                'orderByField' => "localCreationDate",
                'asc'          => false,
                'filters'      => count($filters) ? "[[" . implode("],[", $filters) . "]]" : "",
            ],
        ];

        //print_r($args);

        $response = $ServiceProvider->request("GET",NULL, $args );

        if(count($response['Body']->results)) {
	        $CurrentLead['status']        = $response['Body']->results[0][array_search("leadStatus", $response['Body']->header)];
	        $CurrentLead['last_activity'] = $response['Body']->results[0][array_search("lastActivityInfo", $response['Body']->header)];
            $CurrentLead['next_action']   = $response['Body']->results[0][array_search("nextActionInfo", $response['Body']->header)];
	        $CurrentLead['converted']     = $response['Body']->results[0][array_search("converted", $response['Body']->header)];
	        $CurrentLead['created_at']    = $response['Body']->results[0][array_search("creationDate", $response['Body']->header)];
        } else {
	        $CurrentLead['created_at'] = strftime("%Y-%m-%dT%H:%M:%S", strtotime($Lead->created_at));
        }

    } else {

	    $CurrentLead['created_at'] = strftime("%Y-%m-%dT%H:%M:%S", strtotime($Lead->created_at));

    	try {
		    $ReserveInteractive = \App\ReserveInteractive::where("lead_id", $Lead->id)->firstOrFail();
		    $ri_response        = json_decode($ReserveInteractive->response);

		    if($ri_response[0]->status == "Failed") {
			    $CurrentLead['reserve_error'] = trim(implode(" ", $ri_response[0]->messages));
		    }
	    } catch (\Exception $e) {}

    }

    switch(true) {
	    case preg_match("/ CST$/", $CurrentLead['created_at']):
		    date_default_timezone_set("America/Chicago");
		    break;
	    case preg_match("/ MST$/", $CurrentLead['created_at']):
		    date_default_timezone_set("America/Denver");
		    break;
	    case preg_match("/ PST$/", $CurrentLead['created_at']):
		    date_default_timezone_set("America/Los_Angeles");
		    break;
	    case preg_match("/ EST$/", $CurrentLead['created_at']):
	    default:
		    date_default_timezone_set("America/New_York");
	    	break;
    }

	$CurrentLead['created_at']        = strftime("%Y-%m-%dT%H:%M:%S", strtotime($CurrentLead['created_at']));
	$CurrentLead['assigned_to_name']  = \App\User::find($Lead->owner)->name;
	$CurrentLead['assigned_to_email'] = \App\User::find($Lead->owner)->email;

	$tmp = [];
	foreach($headers as $index => $field) {
		$tmp[$index] = isset($CurrentLead[$field]) ? $CurrentLead[$field] : "";
	}
	$Leads[] = $tmp;
}









$filters   = [];
$filters[] = "'creationDate', 'GREATER_THAN_OR_EQUAL_TO', '" . strftime("%m/%d/%Y %I:%M %p", strtotime("2020-01-01T00:00:00")) . "'";

$ServiceProvider = new \App\ServiceProviders\ReserveInteractive();

foreach(["tap_clicks_event_leads", "tap_clicks_member_leads"] as $request) {

    $lead_type = preg_match("/_member_/", $request) ? "member" : "event";
    $requests  = 0;
    $interval  = 100;
    $count     = 0;
    $offset    = $interval*$requests;

    $args = [
        'auth' => [
            env('RESERVE_INTERACTIVE_USERNAME'),
            env('RESERVE_INTERACTIVE_PASSWORD')
        ],
        'query' => [
            'requestName'  => $request,
            'requestGuid'  => md5(date('YmdHis')),
            'maxResults'   => $interval,
            'firstResult'  => $offset,
            'orderByField' => "localCreationDate",
            'asc'          => false,
            'filters'      => count($filters) ? "[[" . implode("],[", $filters) . "]]" : "",
        ],
    ];

    try {

        $response = $ServiceProvider->request("GET", NULL, $args);
        $headers  = $response['Body']->header;

        print_r($headers);

        do {

            foreach($response['Body']->results as $index => $record) {

                print($offset+$index+1 . ": {$record[7]} {$record[8]} ({$record[9]}) - {$record[4]}\n");

                $ri_id = $response['Body']->results[0][array_search("uniqueId", $response['Body']->header)];

                print_r($record);
                print("RI Id: {$ri_id}\n");

                try {
                    $Lead = App\Leads\Base::whereNull("duplicate_of")
                                           ->where("ri_id", $ri_id)
                                           ->firstOrFail();

                    //print_r($Lead->toArray());
                    exit;
                } catch (\Exception $e) {
                    exit;
                }


            }

            $requests++;
            $offset = $interval*$requests;
            $args['query']['firstResult'] = $offset;

            $response = $ServiceProvider->request("GET",NULL, $args );

        } while(count($response['Body']->results));






    } catch (\Exception $e) {

    }
}
















$Leads = array_merge([$headers], $Leads);

$fp = fopen('./tapclicks-reports.csv', 'w');

foreach ($Leads as $row) {
	//print_r($row);
	fputcsv($fp, $row);
}

fclose($fp);

if(!isset($Options['s']) || !$Options['s']) {
    Storage::disk('ftp')->put('tapclicks-reports/tapclicks-reports.csv', fopen('./tapclicks-reports.csv', 'r+'));
}

?>