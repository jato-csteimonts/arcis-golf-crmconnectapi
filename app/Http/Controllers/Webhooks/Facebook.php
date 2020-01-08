<?php

namespace App\Http\Controllers\Webhooks;

use App\Domain;
use App\Field;
use App\Webforms;
use App\Mail\Lead;

use Illuminate\Http\Request;

class Facebook extends Base {

	public function process(Request $request) {

		\Log::info(print_r($request->toArray(),1));

		try {

			$mail_to  = [];
			$mail_bcc = [
				"pdamer@arcisgolf.com",
				"chris.steimonts@gmail.com",
			];

			$WebhookRequest = parent::process($request);

			/*
			return response(serialize($request->toArray()), 200)
				->header('Content-Type', 'text/plain');

			exit;
			*/

			$Lead = new \App\Leads\Facebook();
			$data = $Lead->normalize($request->toArray());

			\Log::info(print_r($data,1));

			$Lead->webhook_request_id  = $WebhookRequest->id;
			$Lead->sub_type            = $data['sub_type'] ?? "";
			$Lead->first_name          = $data['first_name'] ?? "";
			$Lead->last_name           = $data['last_name'] ?? "";
			$Lead->email               = $data['email'] ?? "";
			$Lead->phone               = $data['phone'] ?? "";
			$Lead->source              = $data['source'] ?? "";
			$Lead->campaign_medium_id  = $data['campaign_medium_id'] ?? "";
			$Lead->campaign_term_id    = $data['campaign_term_id'] ?? "";
			$Lead->campaign_name_id    = $data['campaign_name_id'] ?? null;
			$Lead->revenue_category    = $data['revenue_category'] ?? "";

			switch(true) {

				case isset($data['utm_source']) && preg_match("/^([\d]{7})\-([\d]{4})$/", $data['utm_source']):

					$Lead->club_id = $data['club_id'];
					$Club = \App\Club::findOrFail($Lead->club_id);

					/**
						BEGIN: Creating Attribution Code for Reserve Interactive
					**/
					$ClubCode = $Club->site_code;

					try {
						$CampaignMedium     = \App\CampaignMedium::where("code", $Lead->campaign_medium_id)->firstOrFail();
						$CampaignMediumCode = $CampaignMedium->code;
					} catch(\Exception $e) {
						$CampaignMediumCode = "00";
					}

					$RevenueCategory = $Lead->revenue_category ? str_pad($Lead->revenue_category, 2, "0", STR_PAD_LEFT) : "00";

					try {
						$CampaignTerm     = \App\CampaignTerm::where("code", $Lead->campaign_term_id)->firstOrFail();
						$CampaignTermCode = $CampaignTerm->code;
					} catch(\Exception $e) {
						$CampaignTermCode = "0000";
					}

					$data['campaign_attribution'] = "{$ClubCode}{$CampaignMediumCode}{$RevenueCategory}-{$CampaignTermCode}";

					/**
						END: Creating Attribution Code for Reserve Interactive
					**/

					// Get Sales Person
					$SalespersonRole   = \App\UserRole::where("club_id", $Club->id)->where("role", "salesperson")->where("sub_role", $Lead->sub_type)->firstOrFail();
					$Salesperson       = \App\User::findOrFail($SalespersonRole->user_id);
					$Lead->salesperson = $Salesperson->id;

					// Get Owner
					$OwnerRole   = \App\UserRole::where("club_id", $Club->id)->where("role", "owner")->where("sub_role", $Lead->sub_type)->firstOrFail();
					$Owner       = \App\User::findOrFail($OwnerRole->user_id);
					$Lead->owner = $Owner->id;

					$mail_to = array_unique(array_merge([$Owner->email], [$Salesperson->email]));

					break;

				case strstr($data['campaign_attribution'], "DFW"):

					$Club           = new \App\Club();
					$Club->division = "private";
					$Lead->club_id  = null;

					// Get Sales Person
					$Salesperson       = \App\User::findOrFail(267); // Megan Calmes
					$Lead->salesperson = $Salesperson->id;
					// Get Owner
					$Owner       = \App\User::findOrFail(267); // Megan Calmes
					$Lead->owner = $Owner->id;

					//\Log::info(print_r($Lead->toArray(),1));
					//\Log::info(print_r($Owner->toArray(),1));

					$ServiceProvider = new \App\ServiceProviders\ReserveInteractive();

					list($first_name, $last_name) = explode(" ", $Owner->name, 2);

					$args = [
						'auth' => [
							env('RESERVE_INTERACTIVE_USERNAME'),
							env('RESERVE_INTERACTIVE_PASSWORD')
						],
						'query' => [
							'requestName' => "ContactImport",
							'requestGuid' => md5(date('YmdHis')),
							//'mode'        => 'test',
							'mode'        => 'apply',
						],
						'json' => [

							"parameters" => [
								"firstName" => $first_name,
								"lastName"  => $last_name,
							],

							"header" => [
								"contact.firstName",
								"contact.lastName",
								"contact.email",
								"contact.mobilePhone",
							],

							"data" => [
								0 => [
									$Lead->first_name,
									$Lead->last_name,
									$Lead->email,
									$Lead->phone,
								]
							]

						]
					];

					\Log::info(print_r($args,1));

					try {
						$response = $ServiceProvider->request("POST",NULL, $args );

						\Log::info(print_r($response,1));

						if($response['Body']->results[0]->status == "Failed") {
							$messageClass            = new class {};
							$messageClass->status    = "ERROR";
							$messageClass->messages  = "ContactImport - " . $response['Body']->results[0]->messages;
							$messageClass->json      = $args['json'];
							throw new \Exception(json_encode($messageClass));
						}


					} catch (\GuzzleHttp\Exception\ServerException $e) {

						$messageClass            = new class {};
						$messageClass->status    = "ERROR (" . get_class($e) . ")";
						$messageClass->messages  = "ContactImport - " . $e->getResponse()->getBody()->getContents();
						$messageClass->json      = $args['json'];

						throw new \Exception(json_encode($messageClass));

					} catch (\GuzzleHttp\Exception\ClientException $e) {

						$messageClass            = new class {};
						$messageClass->status    = "ERROR (" . get_class($e) . ")";
						$messageClass->messages  = "ContactImport - " . $e->getResponse()->getBody()->getContents();
						$messageClass->json      = $args['json'];

						throw new \Exception(json_encode($messageClass));
					}

					$mail_to[]  = $Owner->email;
					$mail_bcc[] = "rrinella@arcisgolf.com";
					$mail_bcc[] = "Ccrocker@arcisgolf.com";

					break;
				case strstr($data['campaign_attribution'], "PHX"):
				case strstr($data['campaign_attribution'], "Phoenix Area"):

					$Club           = new \App\Club();
					$Club->division = "private";
					$Lead->club_id  = null;

					// Get Sales Person
					$Salesperson       = \App\User::findOrFail(186); // Jason Fortney
					$Lead->salesperson = $Salesperson->id;
					// Get Owner
					$Owner       = \App\User::findOrFail(368); // Tom Brinkman
					$Lead->owner = $Owner->id;

					//\Log::info(print_r($Lead->toArray(),1));
					//\Log::info(print_r($Owner->toArray(),1));

					$ServiceProvider = new \App\ServiceProviders\ReserveInteractive();

					list($first_name, $last_name) = explode(" ", $Owner->name, 2);

					$args = [
						'auth' => [
							env('RESERVE_INTERACTIVE_USERNAME'),
							env('RESERVE_INTERACTIVE_PASSWORD')
						],
						'query' => [
							'requestName' => "ContactImport",
							'requestGuid' => md5(date('YmdHis')),
							//'mode'        => 'test',
							'mode'        => 'apply',
						],
						'json' => [

							"parameters" => [
								"firstName" => $first_name,
								"lastName"  => $last_name,
							],

							"header" => [
								"contact.firstName",
								"contact.lastName",
								"contact.email",
								"contact.mobilePhone",
							],

							"data" => [
								0 => [
									$Lead->first_name,
									$Lead->last_name,
									$Lead->email,
									$Lead->phone,
								]
							]

						]
					];

					\Log::info(print_r($args,1));

					try {
						$response = $ServiceProvider->request("POST",NULL, $args );

						\Log::info(print_r($response,1));

						if($response['Body']->results[0]->status == "Failed") {
							$messageClass            = new class {};
							$messageClass->status    = "ERROR";
							$messageClass->messages  = "ContactImport - " . $response['Body']->results[0]->messages;
							$messageClass->json      = $args['json'];
							throw new \Exception(json_encode($messageClass));
						}


					} catch (\GuzzleHttp\Exception\ServerException $e) {

						$messageClass            = new class {};
						$messageClass->status    = "ERROR (" . get_class($e) . ")";
						$messageClass->messages  = "ContactImport - " . $e->getResponse()->getBody()->getContents();
						$messageClass->json      = $args['json'];

						throw new \Exception(json_encode($messageClass));

					} catch (\GuzzleHttp\Exception\ClientException $e) {

						$messageClass            = new class {};
						$messageClass->status    = "ERROR (" . get_class($e) . ")";
						$messageClass->messages  = "ContactImport - " . $e->getResponse()->getBody()->getContents();
						$messageClass->json      = $args['json'];

						throw new \Exception(json_encode($messageClass));
					}

					$mail_to    = array_unique(array_merge([$Owner->email], [$Salesperson->email]));
					$mail_bcc[] = "rrinella@arcisgolf.com";
					$mail_bcc[] = "Ccrocker@arcisgolf.com";
					$mail_bcc[] = "twatson@arcisgolf.com";

					break;
				default:
					// Get Club
					\Log::info(print_r($data,1));
					\Log::info(print_r($request->toArray(),1));

					//$tmp_data = explode("_", $data['campaign_attribution'], 2);
					$Club = \App\Club::where("site_code", substr($data['utm_term'], 0, 3))->firstOrFail();
					$Lead->club_id = $Club->id;

					/**
						BEGIN: Creating Attribution Code for Reserve Interactive
					**/
					$ClubCode = $Club->site_code;

					try {
						$CampaignMedium     = \App\CampaignMedium::where("code", $Lead->campaign_medium_id)->firstOrFail();
						$CampaignMediumCode = $CampaignMedium->code;
					} catch(\Exception $e) {
						$CampaignMediumCode = "00";
					}

					$RevenueCategory = $Lead->revenue_category ? str_pad($Lead->revenue_category, 2, "0", STR_PAD_LEFT) : "00";

					try {
						$CampaignTerm     = \App\CampaignTerm::where("code", $Lead->campaign_term_id)->firstOrFail();
						$CampaignTermCode = $CampaignTerm->code;
					} catch(\Exception $e) {
						$CampaignTermCode = "0000";
					}

					$data['campaign_attribution'] = "{$ClubCode}{$CampaignMediumCode}{$RevenueCategory}-{$CampaignTermCode}";

					/**
						END: Creating Attribution Code for Reserve Interactive
					**/

					// Get Sales Person
					$SalespersonRole   = \App\UserRole::where("club_id", $Club->id)->where("role", "salesperson")->where("sub_role", $Lead->sub_type)->firstOrFail();
					$Salesperson       = \App\User::findOrFail($SalespersonRole->user_id);
					$Lead->salesperson = $Salesperson->id;

					// Get Owner
					$OwnerRole   = \App\UserRole::where("club_id", $Club->id)->where("role", "owner")->where("sub_role", $Lead->sub_type)->firstOrFail();
					$Owner       = \App\User::findOrFail($OwnerRole->user_id);
					$Lead->owner = $Owner->id;

					$mail_to = array_unique(array_merge([$Owner->email], [$Salesperson->email]));

					break;
			}

			$Lead->data = serialize($data);
			$Lead->save();
			$Lead->refresh();

			if(is_null($Lead->club_id)) {
				$Lead->division = $Club->division;
			}

			$this->pushToCRM($Lead);

			\Log::info("Mailing to...\n'TO' recipients:\n");
			\Log::info(print_r($mail_to,1));
			\Log::info("\n'BCC' recipients:\n");
			\Log::info(print_r($mail_bcc,1));

			\Mail::to($mail_to)->bcc($mail_bcc)->send(new Lead($Owner, $Club, $Lead));
			//\Mail::to(["chris.steimonts@gmail.com"])->send(new Lead($Owner, $Club, $Lead));

		} catch (\Exception $e) {

			if(preg_match("/^\{/", $e->getMessage())) {
				$messageClass = json_decode($e->getMessage());
				$messageClass->FileName = $e->getFile();
				$messageClass->LineNumber = $e->getLine();
			} else {
				$messageClass = new class {};
				$messageClass->message = $e->getMessage();
				$messageClass->fileName = $e->getFile();
				$messageClass->lineNumber = $e->getLine();
				$messageClass->request = $request->toArray();
				$messageClass->lead = $Lead->toArray();
			}

			\Log::info($e->getMessage());
			\Log::info($e->getFile());
			\Log::info($e->getLine());
			/**
			$u = \App\User::find(1);
			$u->notify(new \App\Notifications\ApiError($messageClass));
			abort(412, $e->getMessage());
			**/

		}

	}

}
