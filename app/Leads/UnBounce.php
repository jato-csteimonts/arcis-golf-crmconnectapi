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
		$out['sub_type'] = $out['lead_type'];
		$out['source']   = preg_replace("/^www\./", "", strtolower(parse_url((preg_match("/^http/", $out['page_url']) ? "" : "http://") . $out['page_url'], PHP_URL_HOST)));

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

		//\Log::info(print_r($out,1));

		return $out;

	}

}
