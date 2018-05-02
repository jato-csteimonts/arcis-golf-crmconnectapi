<?php

namespace App\Http\Controllers\Webhooks;

use App\Domain;
use App\Field;
use App\Webforms;

use Illuminate\Http\Request;

class UnBounce extends Base {

	public function process(Request $request) {

		try {

			$WebhookRequest = parent::process($request);

			$Lead = new \App\Leads\UnBounce();
			$data = $Lead->normalize($request->toArray());

			//\Log::info(print_r($data,1));

			$Lead->webhook_request_id = $WebhookRequest->id;
			$Lead->sub_type           = $data['sub_type'];
			$Lead->first_name         = $data['first_name'] ?? null;
			$Lead->last_name          = $data['last_name'] ?? null;
			$Lead->email              = $data['email'];
			$Lead->phone              = $data['phone'] ?? null;
			$Lead->source             = $data['source'];
			$Lead->data               = serialize($data);

			$Club          = \App\Club::where("site_code", $data['site'])->orWhere("name", "LIKE", "%{$data['site']}%")->firstOrFail();
			$Lead->club_id = $Club->id;

			// Get Sales Person
			if($data['salesperson']) {
				$Salesperson = \App\User::where("email", $data['salesperson'])->firstOrFail();
			} else {
				$SalespersonRole   = \App\UserRole::where("club_id",$Club->id)->where("role", "salesperson")->firstOrFail();
				$Salesperson       = \App\User::findOrFail($SalespersonRole->user_id);
			}
			$Lead->salesperson = $Salesperson->id;

			// Get Owner
			if($data['salesperson']) {
				$Owner = \App\User::where("email", $data['owner'])->firstOrFail();
			} else {
				$OwnerRole   = \App\UserRole::where("club_id", $Club->id)->where("role", "owner")->firstOrFail();
				$Owner       = \App\User::findOrFail($OwnerRole->user_id);
			}
			$Lead->owner = $Owner->id;

			//\Log::info(print_r($Lead->toArray(),1));
			$Lead->save();

			$this->pushToCRM($Lead);

			//abort(500, json_encode(["errors" => "Testing new UnBounce webhooks"]));

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

			//\Log::info($e->getMessage());
			$u = \App\User::find(1);
			$u->notify(new \App\Notifications\ApiError($messageClass));
			abort(500, $e->getMessage());
		}

	}

}
