<?php

chdir(__DIR__);
require __DIR__ . '/../bootstrap/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
use Illuminate\Contracts\Console\Kernel;
$app->make(Kernel::class)->bootstrap();

$Options = getopt("d::", []);

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

		$response = $ServiceProvider->request("GET",NULL, $args );

		do {

			foreach($response['Body']->results as $index => $record) {

				print($offset+$index+1 . ": {$record[7]} {$record[8]} ({$record[9]}) - {$record[4]}\n");

				try {
					$ri        = \App\ReserveInteractive::where("response", "LIKE", "%\"{$record[0]}\"%")->firstOrFail();
					$lead      = \App\Lead::find($ri->lead_id);
					$lead_data = unserialize($lead->data);
				} catch (Exception $e) {
					$lead_data = [];
				}

				$data[] = [
					strftime("%m/%d/%Y", strtotime($record[4])),
					$record[6],
					"{$record[2]} ({$record[3]})",
					$lead_type,
					$lead->sub_type ?? "",
					$lead->type ?? "",
					$record[5],
					$lead_data['utm_content'] ?? "",
					$lead_data['utm_medium'] ?? "",
					$lead_data['utm_namme'] ?? "",
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

		//print_r($data);

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

}

Storage::disk('ftp')->put('tapclicks.csv', fopen('./tapclicks.csv', 'r+'));

?>