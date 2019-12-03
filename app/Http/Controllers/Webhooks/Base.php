<?php

namespace App\Http\Controllers\Webhooks;

use App\Domain;
use App\Field;
use App\ReserveInteractive;
use App\Webforms;
use Illuminate\Http\Request;

class Base {

	public function process(Request $request) {

		\Log::info(print_r($request->toArray(),1));

		$WebhookRequest = new \App\WebhookRequest();
		$WebhookRequest->ip = $request->ip();
		$WebhookRequest->data = serialize($request->toArray());
		$WebhookRequest->server_info = serialize($_SERVER);
		$WebhookRequest->save();
		return $WebhookRequest;
	}

	public function pushToCRM(\App\Leads\Base $Lead) {

		if($Lead->duplicate_of) {
			$messageClass            = new class {};
			$messageClass->status    = "ERROR";
			$messageClass->messages  = "Duplicate Lead Detected, not sending to ReserveInteractive";
			$messageClass->lead      = $Lead->toArray();
			throw new \Exception(json_encode($messageClass));
		}

		$mergeData = [
			"lead_type"   => preg_match("/member/i", $Lead->sub_type) ? "member" : "event",
			"lead_name"   => ucwords(strtolower("{$Lead->first_name} {$Lead->last_name}")),
			"owner"       => \App\User::findOrFail($Lead->owner)->email,
			"salesperson" => \App\User::findOrFail($Lead->salesperson)->email,
		];

		if(!is_null($Lead->club_id)) {
			$Club      = \App\Club::findOrFail($Lead->club_id);
			$mergeData = array_merge($mergeData, [
				"site"     => $Club->name,
				"division" => ucwords(strtolower($Club->division)) . " Division",
			]);
		} else {
			$mergeData = array_merge($mergeData, [
				//"site"     => "",
				"division" => ucwords(strtolower($Lead->division)) . " Division",
				//"limitContactsToSite" => false,
			]);
		}

		//\Log::info(print_r($Lead->toArray(),1));
		//\Log::info(print_r($mergeData,1));
		//\Log::info("Normalizing........");

		$json = \App\ReserveInteractive::normalize(array_merge(unserialize($Lead->data), $mergeData));

		//\Log::info(print_r($json,1));
		//throw new Exception();

		$ServiceProvider = new \App\ServiceProviders\ReserveInteractive();

		$args = [
			'auth' => [
				env('RESERVE_INTERACTIVE_USERNAME'),
				env('RESERVE_INTERACTIVE_PASSWORD')
			],
			'query' => [
				'requestName' => (strtolower($Lead->sub_type) == "member" ? "Member" : "Event") . "LeadImport",
				'requestGuid' => md5(date('YmdHis')),
				//'mode'        => 'test',
				'mode'        => 'apply',
			],
			'json' => $json
		];

		\Log::info(print_r($args,1));

		$ReserveInteractive = new \App\ReserveInteractive();

		try {
			$response = $ServiceProvider->request("POST",NULL, $args );

			\Log::info(print_r($response,1));

			$ReserveInteractive->lead_id = $Lead->id;
			$ReserveInteractive->request_name = $args['query']['requestName'];
			$ReserveInteractive->request_json = json_encode($args['json']);
			$ReserveInteractive->response = json_encode($response['Body']->results);
			$ReserveInteractive->save();

			if($response['Body']->results[0]->status == "Failed") {
				$messageClass            = new class {};
				$messageClass->status    = "ERROR";
				$messageClass->messages  = $response['Body']->results[0]->messages;
				$messageClass->json      = $args['json'];
				throw new \Exception(json_encode($messageClass));
			}

		} catch (\GuzzleHttp\Exception\ServerException $e) {

			$ReserveInteractive->lead_id = $Lead->id;
			$ReserveInteractive->request_name = $args['query']['requestName'];
			$ReserveInteractive->request_json = json_encode($args['json']);
			$ReserveInteractive->response = $e->getResponse()->getBody()->getContents();
			$ReserveInteractive->save();

			$messageClass            = new class {};
			$messageClass->status    = "ERROR (" . get_class($e) . ")";
			$messageClass->messages  = $ReserveInteractive->response;
			$messageClass->json      = $args['json'];

			throw new \Exception(json_encode($messageClass));

		} catch (\GuzzleHttp\Exception\ClientException $e) {

			$ReserveInteractive->lead_id = $Lead->id;
			$ReserveInteractive->request_name = $args['query']['requestName'];
			$ReserveInteractive->request_json = json_encode($args['json']);
			$ReserveInteractive->response = $e->getResponse()->getBody()->getContents();
			$ReserveInteractive->save();

			$messageClass            = new class {};
			$messageClass->status    = "ERROR (" . get_class($e) . ")";
			$messageClass->messages  = $ReserveInteractive->response;
			$messageClass->json      = $args['json'];

			throw new \Exception(json_encode($messageClass));
		}

		//throw new \Exception(json_encode(["ERROR" => "Just testing......"]));
	}

}
