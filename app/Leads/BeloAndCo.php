<?php

namespace App\Leads;

class BeloAndCo extends Base {

	public function __construct() {
		$this->setAttribute("type", self::$TYPE_BELOANDCO);
		parent::__construct();
	}

	public function normalize($data = []) {

		$out = [];

		//\Log::info(print_r($data,1));

		$out['sub_type'] = "member";

		foreach ($data as $incoming_field_key => $incoming_field_value) {
			$field = preg_replace(["/^field_member_/", "/_$/"], "", $incoming_field_key);
			$field = preg_replace(["/^field_/", "/_$/"], "", $field);
			//\Log::info("Field: {$field}");
			switch(true) {
				case !is_array($incoming_field_value):
					$value = $incoming_field_value;
					break;
				case $field == "proposed_dates_of_event":
					$times = [];
					if($incoming_field_value['und'][0]['value']) {
						$datetime = [];
						if($incoming_field_value['und'][0]['value']['date']) {
							$datetime[] = $incoming_field_value['und'][0]['value']['date'];
						}
						if($incoming_field_value['und'][0]['value']['time']) {
							$datetime[] = $incoming_field_value['und'][0]['value']['time'];
						}
						$times[] = implode(" at ", $datetime);
					}
					if($incoming_field_value['und'][0]['value2']) {
						$datetime = [];
						if($incoming_field_value['und'][0]['value2']['date']) {
							$datetime[] = $incoming_field_value['und'][0]['value2']['date'];
						}
						if($incoming_field_value['und'][0]['value2']['time']) {
							$datetime[] = $incoming_field_value['und'][0]['value2']['time'];
						}
						$times[] = implode(" at ", $datetime);
					}
					$value = implode(" to ", $times);
					break;
				case $field == "contact_address":
					$value = null;
					$out["Address 1"] = $incoming_field_value['und'][0]['street'];
					$out["Address 2"] = $incoming_field_value['und'][0]['additional'];
					$out["City"] = $incoming_field_value['und'][0]['city'];
					$out["State"] = $incoming_field_value['und'][0]['province'];
					$out["Zip"] = $incoming_field_value['und'][0]['postal_code'];
					continue;
					break;
				case is_array($incoming_field_value['und']):
					$tmp_data  = $incoming_field_value['und'][0];
					$keys = array_keys($tmp_data);
					$value = $tmp_data[$keys[0]];
					break;
				default:
					$value = $incoming_field_value['und'];
					break;
			}

			switch(true) {
				case strstr($field, "phone") && !isset($out['phone']):
					$field = "phone";
					break;
				default: break;
			}

			if($value) {
				$out[$field] = $value;
			}
		}

		$out['source'] = preg_replace("/^www\./", "", strtolower(parse_url((preg_match("/^http/", $out['source']) ? "" : "http://") . $out['source'], PHP_URL_HOST)));

		if(isset($out['event_type'])) {
			$out['sub_type'] = "event";
		}

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

		\Log::info(print_r($out,1));

		return $out;

	}

}
