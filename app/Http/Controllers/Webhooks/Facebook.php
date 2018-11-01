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

			$WebhookRequest = parent::process($request);

			/*
			return response(serialize($request->toArray()), 200)
				->header('Content-Type', 'text/plain');

			exit;
			*/

			$Lead = new \App\Leads\Facebook();
			$data = $Lead->normalize($request->toArray());

			//\Log::info(print_r($data,1));
			//exit;

			$Lead->webhook_request_id = $WebhookRequest->id;
			$Lead->sub_type           = $data['sub_type'] ?? "";
			$Lead->first_name         = $data['first_name'] ?? "";
			$Lead->last_name          = $data['last_name'] ?? "";
			$Lead->email              = $data['email'] ?? "";
			$Lead->phone              = $data['phone'] ?? "";
			$Lead->source             = $data['source'] ?? "";

			// Get Club
			$tmp_data = explode("_", $data['campaign_attribution'], 2);
			$Club = \App\Club::where("site_code", $tmp_data[0])->firstOrFail();
			$Lead->club_id = $Club->id;

			// Get Sales Person
			$SalespersonRole   = \App\UserRole::where("club_id", $Club->id)->where("role", "salesperson")->where("sub_role", $Lead->sub_type)->firstOrFail();
			$Salesperson       = \App\User::findOrFail($SalespersonRole->user_id);
			$Lead->salesperson = $Salesperson->id;

			// Get Owner
			$OwnerRole   = \App\UserRole::where("club_id", $Club->id)->where("role", "owner")->where("sub_role", $Lead->sub_type)->firstOrFail();
			$Owner       = \App\User::findOrFail($OwnerRole->user_id);
			$Lead->owner = $Owner->id;

			$Lead->data = serialize($data);
			$Lead->save();
			//\Log::info(print_r($Lead->toArray(),1));

			$this->pushToCRM($Lead);

			\Mail::to([
				$Owner->email
			])->bcc([
				"pdamer@arcisgolf.com",
				"chris.steimonts@gmail.com"
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
			\Log::info($e->getFile());
			\Log::info($e->getLine());
			$u = \App\User::find(1);
			$u->notify(new \App\Notifications\ApiError($messageClass));
			abort(412, $e->getMessage());

		}

	}

}
