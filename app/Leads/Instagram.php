<?php

namespace App\Leads;

class Instagram extends Base {

	public function __construct() {
		$this->setAttribute("type", self::$TYPE_INSTAGRAM);
		parent::__construct();
	}

	public function normalize($data = []) {

		$data = isset($data[0]) ? $data[0] : $data;

		\Log::info(print_r($data,1));

		preg_match("/^([\d]{3})([\d]{2})([\d]{2})\-([\d]{4})$/", $data['utm_source'], $code_data);

		$club_id          = $code_data[1];
		$campaign_medium  = $code_data[2];
		$revenue_category = $code_data[3];
		$campaign_term    = $code_data[4];

		$out                         = [];
		$out['campaign_attribution'] = $data['campaign_name'] ?? $data['campaign_attribution'] ?? "";
		$out['club_id']              = \App\Club::where("site_code", $club_id)->first()->id;
		$out['campaign_medium_id']   = \App\CampaignMedium::where("code", $campaign_medium)->first()->id;
		$out['campaign_term_id']     = \App\CampaignTerm::where("code", $campaign_term)->first()->id;
		$out['revenue_category']     = $revenue_category;
		$out['sub_type']             = $revenue_category == "01" ? "member" : "event";
		$out['source']               = "Instagram Lead Ad: " . $out['campaign_attribution'];
		$out['email']                = $data['email'] ?? "";
		$out['first_name']           = $data['first_name'] ?? "";
		$out['last_name']            = $data['last_name'] ?? "No Last Name Provided";
		$out['phone']                = preg_replace("/([^0-9]+)/", "", $data['phone_number'] ?? "");
		$out['utm_source']           = $data['utm_source'] ?? "";
		$out['utm_medium']           = $data['utm_medium'] ?? "";
		$out['utm_campaign']         = $data['utm_campaign'] ?? "";
		$out['utm_term']             = $data['utm_term'] ?? "";
		$out['utm_content']          = $data['utm_content'] ?? "";

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
