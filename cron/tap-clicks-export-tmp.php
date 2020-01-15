<?php

chdir(__DIR__);
require __DIR__ . '/../bootstrap/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
use Illuminate\Contracts\Console\Kernel;
$app->make(Kernel::class)->bootstrap();

$Options = getopt("d::s::", []);

$filters = [];

if ( isset($Options['d']) && $Options['d'] != "D" ) {
	@list( $start, $stop ) = explode( ",", $Options['d'] );
	$filters[] = "'creationDate', 'GREATER_THAN_OR_EQUAL_TO', '" . strftime("%m/%d/%Y %I:%M %p", strtotime($start)) . "'";
	if($stop) {
		$filters[] = "'creationDate', 'LESS_THAN', '" . strftime("%m/%d/%Y %I:%M %p", strtotime($stop)) . "'";
	} else {
		$filters[] = "'creationDate', 'LESS_THAN', '" . strftime("%m/%d/%Y 12:00 AM", strtotime("Today")) . "'";
	}
} else {
	$filters[] = "'creationDate', 'GREATER_THAN_OR_EQUAL_TO', '" . strftime("%m/%d/%Y 12:00 AM", strtotime("Yesterday")) . "'";
	$filters[] = "'creationDate', 'LESS_THAN', '" . strftime("%m/%d/%Y 12:00 AM", strtotime("Today")) . "'";
}

$ServiceProvider = new \App\ServiceProviders\ReserveInteractive();

$data = [];

/**
$data[] = [
"Unique ID",
"Site",
"Site Code",
"Attribution Code",
"Lead Status",
"Lead First Name",
"Lead Last Name",
"Lead Email",
"Created Date",
"Lead Type",
"utm_content",
"utm_medium",
"utm_name",
"utm_term",
"utm_source",
];
 **/

$data[] = [
	"Site_Code",
	"Ri_Lead_Id",
	"created_date",
	"attribution_code",
	"site",
	"lead_type",
	"lead_sub_type",
	"lead_source",
	"lead_status",
	"utm_content",
	"utm_medium",
	"utm_name",
	"utm_term",
	"utm_source",
];

foreach(["tap_clicks_member_leads"/**,"tap_clicks_event_leads"**/] as $request) {

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

		$response = $ServiceProvider->request("GET",NULL, $args );

		do {

			foreach($response['Body']->results as $index => $record) {

				print($offset+$index+1 . ": {$record[7]} {$record[8]} ({$record[9]}) - {$record[4]}\n");

				$lead      = new stdClass();
				$lead_data = [];

				try {
					$ri        = \App\ReserveInteractive::where("response", "LIKE", "%\"{$record[0]}\"%")->firstOrFail();
					$lead      = \App\Lead::find($ri->lead_id);
					$lead_data = unserialize($lead->data);
					//print_r($lead->toArray());
				} catch (Exception $e) {






					print_r($record);
					//exit;

				}

				continue;

				//print_r($lead_data);

				$attribution_code = $lead_data['utm_term'] ?? "";

				if(!preg_match("/^([\d]{7})\-([\d]{4})$/", trim($attribution_code))) {

					$old_attribution_code = $attribution_code;
					$site_code            = $record[2];

					switch(true) {
						case ($lead->type ?? null) == "beloandco":
							$medium_verbose = "website";
							break;
						case ($lead->type ?? null) == "unbounce":
							switch(true) {
								case ($lead_data['utm_medium'] ?? null) == "facebook":
								case ($lead_data['utm_medium'] ?? null) == "instagram":
								case ($lead_data['utm_medium'] ?? null) == "eltoro":
								case ($lead_data['utm_medium'] ?? null) == "gmb":
								case ($lead_data['utm_medium'] ?? null) == "email":
									$medium_verbose = $lead_data['utm_medium'];
									break;
								case ($lead_data['utm_medium'] ?? null) == "website":
								case ($lead_data['utm_medium'] ?? null) == "cta_box":
								default:
									$medium_verbose = "website";
									break;
							}
							break;
						case strstr(strtolower($record[18]), "wedding"):
							switch(true) {
								case strstr(strtolower($record[19]), "knot"):
									$medium_verbose = "theknot";
									break;
								case strstr(strtolower($record[19]), "wire"):
									$medium_verbose = "weddingwire";
									break;
							}
							break;
					}

					$medium = null;
					if($medium_verbose ?? false) {
						$medium = \App\CampaignMedium::where("slug", $medium_verbose)->value("code");
						print("Medium: {$medium_verbose} ({$medium})\n");
					} else {
						print("Missing Medium, skipping {$record[7]} {$record[8]} ({$record[9]}) - {$record[4]}.....\n");
						continue;
					}

					switch(true) {
						case $lead->sub_type ?? null == "member":
							$revenue_category = "01";
							break;
						case $lead->sub_type ?? null == "wedding":
						case strstr(strtolower($record[18]), "wedding"):
							$revenue_category = "02";
							break;
						case $lead->sub_type ?? null == "private":
							$revenue_category = "03";
							break;
					}

					$attribution_code = "{$site_code}{$medium}{$revenue_category}";

					//print("Old: {$old_attribution_code}\n");
					//print("New: {$attribution_code}\n");
					//print("---------------------\n");

				}

				continue;

				//print_r($lead_data);

				$data[] = [
					$record[2],
					$record[0],
					strftime("%m/%d/%Y", strtotime($record[4])),
					//$record[6],
					$lead_data['utm_term'] ?? "",
					"{$record[2]} ({$record[3]})",
					$lead_type,
					$lead->sub_type ?? "",
					$lead->type ?? "",
					$record[5],
					$lead_data['utm_content'] ?? "",
					$lead_data['utm_medium'] ?? "",
					$lead_data['utm_name'] ?? "",
					$lead_data['utm_term'] ?? "",
					$lead_data['utm_source'] ?? "",
				];

				/**
				$data[] = [
				$record[0],
				$record[2],
				"{$record[1]} ({$record[3]})",
				$record[6],
				$record[5],
				$record[7],
				$record[8],
				$record[9],
				strftime("%m/%d/%Y", strtotime($record[4])),
				$record[10],
				$lead_data['utm_content'] ?? "",
				$lead_data['utm_medium'] ?? "",
				$lead_data['utm_namme'] ?? "",
				$lead_data['utm_term'] ?? "",
				$lead_data['utm_source'] ?? "",
				];
				 **/

			}

			$requests++;
			$offset = $interval*$requests;
			$args['query']['firstResult'] = $offset;

			$response = $ServiceProvider->request("GET",NULL, $args );

		} while(count($response['Body']->results));

		if(isset($Options['s']) && $Options['s']) {
			print_r($data);
		}

		$fp = fopen('./tapclicks.csv', 'w');

		foreach ($data as $row) {
			fputcsv($fp, $row);
		}

		fclose($fp);

	} catch (\GuzzleHttp\Exception\ServerException $e) {
		print_r($e->getResponse()->getBody()->getContents());
	} catch (\GuzzleHttp\Exception\ClientException $e) {
		print_r($e->getResponse()->getBody()->getContents());
	}

	break;

}

if(!isset($Options['s']) || !$Options['s']) {
	Storage::disk('ftp')->put('tapclicks.csv', fopen('./tapclicks.csv', 'r+'));
}

?>