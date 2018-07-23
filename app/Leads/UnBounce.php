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

		$out['sub_type'] = $out['lead_type'];
		$out['source']   = strtolower(parse_url((preg_match("/^http/", $out['page_url']) ? "" : "http://") . $out['page_url'], PHP_URL_HOST));

		if(isset($out['club'])) {
			$out['site'] = $out['club'];
			unset($out['club']);
		}

		//\Log::info(print_r($out,1));

		return $out;

	}

}
