<?php

namespace App\Http\Controllers\Webhooks;

use App\Domain;
use App\Field;
use App\Webforms;
use App\Mail\Lead;

use Illuminate\Http\Request;

class BeloAndCo extends Base {

	public function process(Request $request) {

		try {

			$WebhookRequest = parent::process($request);

			$Lead = new \App\Leads\BeloAndCo();
			$data = $Lead->normalize($request->toArray());

			\Log::info(print_r($data,1));

			$Lead->webhook_request_id = $WebhookRequest->id;
			$Lead->sub_type           = $data['sub_type'] ?? "";
			$Lead->first_name         = $data['first_name'] ?? "";
			$Lead->last_name          = $data['last_name'] ?? "";
			$Lead->email              = $data['email'] ?? "";
			$Lead->phone              = $data['phone'] ?? "";
			$Lead->source             = $data['source'] ?? "";
			$Lead->campaign_term_id   = $data['campaign_term_id'] ?? null;
			$Lead->campaign_medium_id = $data['campaign_medium_id'] ?? null;
			$Lead->campaign_name_id   = $data['campaign_name_id'] ?? null;
			$Lead->revenue_category   = $data['revenue_category'] ?? null;

			// Get Club
			$Domain        = \App\Domain::where("domain", $Lead->source)->firstOrFail();
			$Lead->club_id = $Domain->club_id;
			$Club          = \App\Club::findOrFail($Lead->club_id);

			/**
				BEGIN: Creating Attribution Code for Reserve Interactive
			**/
			$ClubCode = $Club->site_code;

			try {
				$CampaignMedium     = \App\CampaignMedium::where("id", $Lead->campaign_medium_id)->firstOrFail();
				$CampaignMediumCode = $CampaignMedium->code;
			} catch(\Exception $e) {
				$CampaignMediumCode = "00";
			}

			$RevenueCategory = $Lead->revenue_category ? str_pad($Lead->revenue_category, 2, "0", STR_PAD_LEFT) : "00";

			try {
				$CampaignTerm     = \App\CampaignTerm::where("id", $Lead->campaign_term_id)->firstOrFail();
				$CampaignTermCode = $CampaignTerm->code;
			} catch(\Exception $e) {
				$CampaignTermCode = "0000";
			}

			$data['campaign_attribution'] = "{$ClubCode}{$CampaignMediumCode}{$RevenueCategory}-{$CampaignTermCode}";

			/**
				END: Creating Attribution Code for Reserve Interactive
			**/

			// Get Sales Person
			$SalespersonRole   = \App\UserRole::where("club_id",$Domain->club_id)->where("role", "salesperson")->where("sub_role", $Lead->sub_type)->firstOrFail();
			$Salesperson       = \App\User::findOrFail($SalespersonRole->user_id);
			$Lead->salesperson = $Salesperson->id;

			// Get Owner
			$OwnerRole   = \App\UserRole::where("club_id", $Domain->club_id)->where("role", "owner")->where("sub_role", $Lead->sub_type)->firstOrFail();
			$Owner       = \App\User::findOrFail($OwnerRole->user_id);
			$Lead->owner = $Owner->id;

			\Log::info("*************************");
			\Log::info(print_r($data,1));
			\Log::info("*************************");

			$Lead->data = serialize($data);

			\Log::info(print_r($Lead->toArray(),1));

			$Lead->save();
			$Lead->refresh();

			$this->pushToCRM($Lead);

			$Club = \App\Club::find($Lead->club_id);

			\Mail::to([
				$Owner->email
			])->bcc([
				"pdamer@arcisgolf.com",
				"chris.steimonts@gmail.com",
				//"rrinella@arcisgolf.com",
				//"Ccrocker@arcisgolf.com",
			])->send(new Lead($Owner, $Club, $Lead));

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
			/**
			$u = \App\User::find(1);
			$u->notify(new \App\Notifications\ApiError($messageClass));
			abort(412, $e->getMessage());
			**/
		}

	}

}
