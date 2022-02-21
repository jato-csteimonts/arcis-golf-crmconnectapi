<?php

namespace App\Http\Controllers\Webhooks;

use App\Domain;
use App\Field;
use App\Webforms;
use App\Mail\Lead;
use Twilio\Rest\Client;
use \Log;

use Illuminate\Http\Request;

class v2 extends Base {

	public function process(Request $request) {

		try {

			$mail_to  = [];
			$mail_bcc = [
				"pdamer@arcisgolf.com",
				"chris.steimonts@gmail.com",
			];

			$WebhookRequest = parent::process($request);
			//$Lead = new \App\Leads\Pradera();
			$Lead = new \App\Leads\v2();
			$data = $Lead->normalize($request->toArray());

			Log::info(print_r($data,1));
			Log::info(print_r($Lead,1));

			$Lead->webhook_request_id = $WebhookRequest->id;
			$Lead->sub_type           = $data['sub_type'] ?? "";
			$Lead->first_name         = $data['first_name'] ?? "";
			$Lead->last_name          = $data['last_name'] ?? "";
			$Lead->email              = $data['email'] ?? "";
			$Lead->phone              = $data['phone'] ?? "";
			$Lead->source             = $data['source'] ?? "";

			// Get Club
			switch(true) {
				case isset($data['club_name']):
					$Club = \App\Club::where("name", $data['club_name'])->firstOrFail();
					break;
				case isset($data['site_code']):
					$Club = \App\Club::where("site_code", $data['site_code'])->firstOrFail();
					break;
			}

			$Lead->club_id = $Club->id;

			// Get Sales Person
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

			/**
			\Log::info(print_r($Lead->toArray(),1));
			if($WebhookRequest->ip == "73.157.175.161") {
			exit;
			}
			 **/

			\Log::info("Sending to CRM.....");

			$this->pushToCRM($Lead);

			\Log::info("Mailing to...\n'TO' recipients:\n");
			\Log::info(print_r($mail_to,1));
			\Log::info("\n'BCC' recipients:\n");
			\Log::info(print_r($mail_bcc,1));

			\Mail::to($mail_to)->bcc($mail_bcc)->send(new Lead($Owner, $Club, $Lead));
			//\Mail::to(["chris.steimonts@gmail.com"])->send(new Lead($Owner, $Club, $Lead));

			//$Owner->phone = "5038812297";

			if($Owner->phone) {
				$sid    = 'AC08c40b1284fd703d811fcd01c0eddf9b';
				$token  = 'd3f1333fcce590636420451bd4bcdc63';
				$client = new Client($sid, $token);

				$body = view('sms.lead', ['club' => $Club, 'user' => $Owner, 'lead' => $Lead])->render();

				$client->messages->create(
					"+1{$Owner->phone}", [
						'messagingServiceSid' => 'MG86b058c4cc5cf6a8b2e2247c946696e1',
						'body' => $body
					]
				);
				Log::info("Sent Text!!!");
			}

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
			\Log::info(print_r($messageClass,1));
			/**
			$u = \App\User::find(1);
			$u->notify(new \App\Notifications\ApiError($messageClass));
			abort(412, $e->getMessage());
			 **/
		}

	}

}
