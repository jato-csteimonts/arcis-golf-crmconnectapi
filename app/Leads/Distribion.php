<?php

namespace App\Leads;

class Distribion extends Base {

	public function __construct() {
		$this->setAttribute("type", self::$TYPE_DISTRIBION);
		parent::__construct();
	}

	public function normalize($data = []) {

		$out    = [];
		$data   = array_values($data['answer']);

		//\Log::info(print_r($data,1));

		if(count($data) == 7) {
			$data = array_merge(array_slice($data, 0, 4), [[]], array_slice($data, 4));
			//\Log::info(print_r($data,1));
		}

		$out['sub_type']    = "member"; // For now, all leads coming from DISTRIBION are of the "member" variety....
		$out['first_name']  = $data[0];
		$out['last_name']   = $data[1];
		$out['email']       = $data[2];
		$out['phone']       = $data[3];
		$out['interests']   = is_array($data[4]) ? implode(", ", (array_keys($data[4]))) : "";
		$out['comments']    = $data[5];
		$out['campaign']    = $data[6];
		$out['source']      = strtolower(parse_url((preg_match("/^http/", $data[7]) ? "" : "http://") . $data[7], PHP_URL_HOST));

		//\Log::info(print_r($out,1));

		return $out;

	}

}
