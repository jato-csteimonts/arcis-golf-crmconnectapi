<?php

namespace App\Http\Controllers\Webhooks;

use App\Domain;
use App\Field;
use App\Webforms;

use Illuminate\Http\Request;

class Distribion extends Base {

	public function process(Request $request) {

		try {

			$WebhookRequest = parent::process($request);

			$Lead = new \App\Leads\Distribion();
			$data = $Lead->normalize($request->toArray());

			//\Log::info(print_r($data,1));

			$Lead->webhook_request_id = $WebhookRequest->id;
			$Lead->sub_type           = $data['sub_type'];
			$Lead->first_name         = $data['first_name'];
			$Lead->last_name          = $data['last_name'];
			$Lead->email              = $data['email'];
			$Lead->phone              = $data['phone'];
			$Lead->source             = $data['source'];

			// Get Club
			$Domain        = \App\Domain::where("domain", $Lead->source)->firstOrFail();
			$Lead->club_id = $Domain->club_id;

			// Get Sales Person
			$SalespersonRole   = \App\UserRole::where("club_id",$Domain->club_id)->where("role", "salesperson")->firstOrFail();
			$Salesperson       = \App\User::findOrFail($SalespersonRole->user_id);
			$Lead->salesperson = $Salesperson->id;

			// Get Owner
			$OwnerRole   = \App\UserRole::where("club_id", $Domain->club_id)->where("role", "owner")->firstOrFail();
			$Owner       = \App\User::findOrFail($OwnerRole->user_id);
			$Lead->owner = $Owner->id;

			$Lead->data = serialize($data);

			//\Log::info(print_r($Lead->toArray(),1));
			$Lead->save();

			$this->pushToCRM($Lead);

		} catch (\Exception $e) {
			$u = \App\User::find(1);
			$u->notify(new \App\Notifications\ApiError(json_decode($e->getMessage())));
			abort(500, $e->getMessage());
		}

	}

}
