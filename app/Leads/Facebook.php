<?php

namespace App\Leads;

class Facebook extends Base {

	public function __construct() {
		$this->setAttribute("type", self::$TYPE_FACEBOOK);
		parent::__construct();
	}

	public function normalize($data = []) {

		$data = isset($data[0]) ? $data[0] : $data;

		$name_info                   = explode(" ", $data['full_name'], 2);
		$out                         = [];
		$out['campaign_attribution'] = $data['campaign_name'];
		$out['sub_type']             = "event";
		$out['source']               = "Facebook Lead Ad: " . $data['campaign_name'];
		$out['email']                = $data['email'];
		$out['first_name']           = $name_info[0];
		$out['last_name']            = $name_info[1] ?? "No Last Name Provided";
		$out['phone']                = preg_replace("/([^0-9]+)/", "", $data['phone_number']);
		$out['last_name']            = $name_info[1] ?? "No Last Name Provided";
		$out['company_title']        = $data['company_name'];

		switch(true) {
			case !$out['email']:
				throw new \Exception("Missing required email address, aborting...");
				break;
			case !filter_var($out['email'], FILTER_VALIDATE_EMAIL):
				throw new \Exception("Invalid email address ({$out['email']}), aborting...");
				break;
			case !$out['last_name']:
				throw new \Exception("Reserve Interactive requires a contact to have a last name. No last name provided, aborting...");
				break;
		}

		//\Log::info(print_r($out,1));

		return $out;

	}

}
