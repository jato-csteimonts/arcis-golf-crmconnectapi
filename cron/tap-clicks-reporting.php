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
	"medium",
	"revenue_category",
	"campaign_term",
	"campaign_name",
	"status",
	"next_action",
	"converted",
	"assigned_to_name",
	"assigned_to_email",
	"reserve_error",
	"created_at"
];

$Leads = [];

foreach(App\Leads\Base::whereNull("duplicate_of")
                      ->where("created_at", ">=", "2020-01-01T00:00:00")
                      ->where("sub_type", "!=", "member")
                      ->orderBy("created_at", "DESC")
                      ->get() as $index => $Lead) {

	/**
	if($index >= 2) {
		break;
	}
	**/

	//print_r($Lead->toArray());

	$CurrentLead = [];
	$CurrentLead['lead_id'] = $Lead->id;
	$CurrentLead['club_id'] = \App\Club::find($Lead->club_id)->site_code;
	$CurrentLead['club_name'] = \App\Club::find($Lead->club_id)->name;
	$CurrentLead['lead_first_name'] = $Lead->first_name;
	$CurrentLead['lead_last_name'] = $Lead->last_name;
	$CurrentLead['lead_email'] = $Lead->email;
	$CurrentLead['medium'] = $Lead->campaign_medium_id ? \App\CampaignMedium::find($Lead->campaign_medium_id)->code : "";
	$CurrentLead['campaign_term'] = $Lead->campaign_term_id ? \App\CampaignTerm::find($Lead->campaign_term_id)->code : "";
	$CurrentLead['campaign_name'] = $Lead->campaign_name_id ? \App\CampaignName::find($Lead->campaign_name_id)->slug : "";
	$CurrentLead['revenue_category'] = str_pad($Lead->revenue_category, 2, "0", STR_PAD_LEFT);

    print(($index+1) . "...");

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
	        $CurrentLead['next_action']   = $response['Body']->results[0][array_search("lastActivityInfo", $response['Body']->header)];
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

$Leads = array_merge([$headers], $Leads);

$fp = fopen('./tapclicks-reports.csv', 'w');

foreach ($Leads as $row) {
	//print_r($row);
	fputcsv($fp, $row);
}

fclose($fp);

if(!isset($Options['s']) || !$Options['s']) {
    Storage::disk('ftp')->put('tapclicks-reports.csv', fopen('./tapclicks-reports.csv', 'r+'));
}

?>