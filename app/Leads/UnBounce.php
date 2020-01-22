<?php

namespace App\Leads;

class UnBounce extends Base {

	public function __construct() {
		$this->setAttribute("type", self::$TYPE_UNBOUNCE);
		parent::__construct();
	}

	public function normalize($data = []) {

		$out  = [];
		$data = array_merge( $data, (array) json_decode( $data['data_json'] ) );

		//\Log::info(print_r($data,1));

		foreach ($data as $k => $curr_data) {
			if(is_array($curr_data)) {
				$out[strtolower($k)] = $curr_data[0];
			}
		}

		$out['page_url'] = isset($out['page_url']) ? $out['page_url'] : $data['page_url'];
		$out['sub_type'] = $out['lead_type'] ?? null;
		$out['source']   = preg_replace("/^www\./", "", strtolower(parse_url((preg_match("/^http/", $out['page_url']) ? "" : "http://") . $out['page_url'], PHP_URL_HOST)));
		$out['test']     = $data['test'] ?? 0;

		if(is_null($out['sub_type'])) {
			$host = parse_url($out['page_url'], PHP_URL_HOST);
			switch(true) {
				case preg_match("/^join\./", $host):
					$out['sub_type'] = "member";
					break;
				case preg_match("/^join\./", $host):
					$out['sub_type'] = "member";
					break;
			}
		}

		if(isset($out['club'])) {
			$out['site'] = $out['club'];
			unset($out['club']);
		}

		$out['email'] = trim(str_replace(" ", "", $out['email']));

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

		switch(true) {

			case preg_match("/^([\d]{3})([\d]{2})([\d]{2})\-([\d]{3,4})$/", $out['utm_source'] ?? "", $code_data):
			case preg_match("/^([\d]{3})([\d]{2})([\d]{2})\-([\d]{3,4})$/", $out['ip_address'] ?? "", $code_data):
			case preg_match("/^([\d]{3})([\d]{2})([\d]{2})\-([\d]{3,4})$/", $out['utm_term'] ?? "", $code_data):
			case preg_match("/^([\d]{3})([\d]{2})([\d]{2})\-([\d]{3,4})$/", $out['utm_content'] ?? "", $code_data):
			case preg_match("/^([\d]{3})([\d]{2})([\d]{2})\-([\d]{3,4})$/", $out['utm_medium'] ?? "", $code_data):
			case preg_match("/^([\d]{3})([\d]{2})([\d]{2})\-([\d]{3,4})$/", $out['utm_campaign'] ?? "", $code_data):
				$campaign_term_code    = str_pad($code_data[4], 4, "0", STR_PAD_LEFT);
				$campaign_medium_code  = $code_data[2];
				$revenue_category_code = $code_data[3];
				break;

			case preg_match("/^([\d]{3})([\d]{1})([\d]{2})\-([\d]{4})$/", $out['utm_source'] ?? "", $code_data):
			case preg_match("/^([\d]{3})([\d]{1})([\d]{2})\-([\d]{4})$/", $out['ip_address'] ?? "", $code_data):
			case preg_match("/^([\d]{3})([\d]{1})([\d]{2})\-([\d]{4})$/", $out['utm_term'] ?? "", $code_data):
			case preg_match("/^([\d]{3})([\d]{1})([\d]{2})\-([\d]{4})$/", $out['utm_content'] ?? "", $code_data):
			case preg_match("/^([\d]{3})([\d]{1})([\d]{2})\-([\d]{4})$/", $out['utm_medium'] ?? "", $code_data):
			case preg_match("/^([\d]{3})([\d]{1})([\d]{2})\-([\d]{4})$/", $out['utm_campaign'] ?? "", $code_data):
				$campaign_term_code    = $code_data[4];
				$campaign_medium_code  = str_pad($code_data[2], 2, "0", STR_PAD_LEFT);
				$revenue_category_code = $code_data[3];
				break;

			default:
				$campaign_term_code    = null;
				$campaign_medium_code  = null;
				$revenue_category_code = null;

				switch($out['sub_type']) {
					case "private":
					case "corporate":
					case "event":
					case "tournament":
						$revenue_category_code = "03";
						break;
					case "wedding": $revenue_category_code = "02"; break;
					case "member": $revenue_category_code = "01"; break;
				}
				break;

		}

		if(isset($campaign_term_code)) {
			try {
				$CampaignTerm = \App\CampaignTerm::where("code", $campaign_term_code)->firstOrFail();
				$out['campaign_term_id'] = $CampaignTerm->id;
				$out['utm_term']         = $CampaignTerm->slug;
			} catch (\Exception $e) {}
		}

		if(isset($campaign_medium_code)) {
			try {
				$CampaignMedium = \App\CampaignMedium::where("code", $campaign_medium_code)->firstOrFail();
				$out['campaign_medium_id'] = $CampaignMedium->id;
				$out['utm_medium']         = $CampaignMedium->slug;
			} catch (\Exception $e) {}
		}

		if(isset($revenue_category_code)) {
			$out['revenue_category'] = (int)$revenue_category_code;
		}

		if(isset($out['utm_campaign'])) {
			try {
				$out['campaign_name_id'] = \App\CampaignName::where("slug", $out['utm_campaign'])->firstOrFail()->id;
			} catch (\Exception $e) {}
		}

		//\Log::info(print_r($out,1));
		//exit;

		return $out;

	}

}
