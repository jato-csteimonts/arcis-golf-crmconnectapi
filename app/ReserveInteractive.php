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

		foreach($form_data as $k => $v) {
			if(!in_array($k, $used)) {
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

		//Log::info(print_r($this->json,1));

		return $json;

	}

}
