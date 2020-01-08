<?php

namespace App\Http\Controllers\Webhooks;

use App\Domain;
use App\Field;
use App\Webforms;
use App\Mail\Lead;

use Illuminate\Http\Request;

class Instagram extends Base {

	public function process(Request $request) {

		\Log::info(print_r($request->toArray(),1));

		try {

			$mail_to  = [];
			$mail_bcc = [
				"pdamer@arcisgolf.com",
				"chris.steimonts@gmail.com",
			];

			$WebhookRequest = parent::process($request);

			$Lead = new \App\Leads\Instagram();
			$data = $Lead->normalize($request->toArray());

			\Log::info(print_r($data,1));

			$Lead->webhook_request_id  = $WebhookRequest->id;
			$Lead->sub_type            = $data['sub_type'] ?? "";
			$Lead->first_name          = $data['first_name'] ?? "";
			$Lead->last_name           = $data['last_name'] ?? "";
			$Lead->email               = $data['email'] ?? "";
			$Lead->phone               = $data['phone'] ?? "";
			$Lead->source              = $data['source'] ?? "";
			$Lead->club_id             = $data['club_id'] ?? "";
			$Lead->campaign_medium_id  = $data['campaign_medium_id'] ?? "";
			$Lead->campaign_term_id    = $data['campaign_term_id'] ?? "";
			$Lead->campaign_name_id    = $data['campaign_name_id'] ?? null;
			$Lead->revenue_category    = $data['revenue_category'] ?? "";

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

			$SalespersonRole   = \App\UserRole::where("club_id", $Club->id)->where("role", "salesperson")->where("sub_role", $Lead->sub_type)->firstOrFail();
			$Salesperson       = \App\User::findOrFail($SalespersonRole->user_id);
			$Lead->salesperson = $Salesperson->id;

			// Get Owner
			$OwnerRole   = \App\UserRole::where("club_id", $Club->id)->where("role", "owner")->where("sub_role", $Lead->sub_type)->firstOrFail();
			$Owner       = \App\User::findOrFail($OwnerRole->user_id);
			$Lead->owner = $Owner->id;

			$mail_to = array_unique(array_merge([$Owner->email], [$Salesperson->email]));

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
