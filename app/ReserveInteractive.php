<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ReserveInteractive extends Model {

	//protected $table = 'ri_transactions';

	public static function normalize($data = []) {

		\Log::info(print_r($data, 1));

		$form_data = $data;

		$lead_type = strtolower($form_data['lead_type']);

		//\Log::info("Lead Type: {$lead_type}");

		$header = [];
		$data   = [];
		$used   = [];
		$misc   = [];

		foreach(\Config::get("ri.fields.{$lead_type}") AS $subtype => $collection) {
			foreach($collection as $ri_field => $metadata) {
				foreach($metadata['possible'] as $ub_field) {
					if(isset($form_data[$ub_field])) {
						if(!in_array($ri_field, $header)) {
							$header[] = $ri_field;
							$data[]   = $form_data[$ub_field];
							$used[]   = $ub_field;
						}
					}
				}
			}
		}

		$misc_ignore = [
			"form",
			"captcha",
			"honeypot"
		];

		$regex = '/(' .implode('|', $misc_ignore) .')/i';

		/**
		\Log::info("***** STEIN *****");
		\Log::info(print_r($form_data,1));
		\Log::info(print_r($data,1));
		\Log::info("***** STEIN *****");
		exit;
		**/

		$skip = [
			'event_type',
			'site_code',
			'public',
			'campaign_medium_id',
			'revenue_category',
			'lead_type',
		];

		foreach($form_data as $k => $v) {
			if(!in_array($k, $used) && !preg_match($regex, $k) && !in_array($k, $skip) && $v) {
				$misc[] = ucwords(str_replace("_", " ", $k)) . " : {$v}";
			}
		}

		if(count($misc)) {
			$header[] = \Config::get("ri.fields.misc.{$lead_type}");
			$data[]   = implode("<br />\n", $misc);
		}

		$status_field = \Config::get("ri.fields.status.{$lead_type}");
		$fields = \Config::get("ri.fields.{$lead_type}.{$lead_type}-lead");
		$header[] = $status_field;
		$data[]   = $fields[$status_field]['values']['new'];

		$json['header'] = $header;
		$json['data'][] = $data;

		//\Log::info(print_r($json,1));

		return $json;

	}

}
