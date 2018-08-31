<?php

namespace App\Leads;

class Distribion extends Base {

	public function __construct() {
		$this->setAttribute("type", self::$TYPE_DISTRIBION);
		parent::__construct();
	}

	public function normalize($data = []) {

		$out    = [];
		$data   = array_values($data);

		//\Log::info(print_r($data,1));

		if(count($data) == 7) {
			$data = array_merge(array_slice($data, 0, 4), ["",[]], array_slice($data, 4));
			//\Log::info(print_r($data,1));
		}

		if(count($data) == 8) {
			$data = array_merge(array_slice($data, 0, 4), [[]], array_slice($data, 4));
			//\Log::info(print_r($data,1));
		}

		//\Log::info(print_r($data,1));

		$out['sub_type']        = "member"; // For now, all leads coming from DISTRIBION are of the "member" variety....
		$out['first_name']      = $data[0];
		$out['last_name']       = $data[1];
		$out['email']           = $data[2];
		$out['phone']           = $data[3];
		$out['preferred_date']  = (!isset($data[4]['dropdown_value']) || $data[4]['dropdown_value'] == "NULL") ? "n/a" : $data[4]['dropdown_value'];
		$out['interests']       = is_array($data[5]) ? implode(", ", (array_keys($data[5]))) : "";
		$out['comments']        = $data[6];
		$out['campaign']        = $data[7];
		$out['source']          = strtolower(parse_url((preg_match("/^http/", $data[count($data)-1]) ? "" : "http://") . $data[count($data)-1], PHP_URL_HOST));

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
