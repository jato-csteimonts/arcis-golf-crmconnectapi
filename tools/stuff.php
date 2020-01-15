<?php

chdir(__DIR__);
require __DIR__ . '/../bootstrap/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
use Illuminate\Contracts\Console\Kernel;
$app->make(Kernel::class)->bootstrap();

$count = 0;

$missing_terms = [];

foreach(App\Leads\Base::whereNull("duplicate_of")->whereNull("revenue_category")->where("type", App\Leads\Base::$TYPE_UNBOUNCE)->orderBy("created_at", "DESC")->get() as $Lead) {

	$data = unserialize($Lead->data);

	switch(true) {

		case preg_match("/^([\d]{3})([\d]{2})([\d]{2})\-([\d]{3,4})$/", $data['utm_source'] ?? "", $code_data):
		case preg_match("/^([\d]{3})([\d]{2})([\d]{2})\-([\d]{3,4})$/", $data['ip_address'] ?? "", $code_data):
		case preg_match("/^([\d]{3})([\d]{2})([\d]{2})\-([\d]{3,4})$/", $data['utm_term'] ?? "", $code_data):
		case preg_match("/^([\d]{3})([\d]{2})([\d]{2})\-([\d]{3,4})$/", $data['utm_content'] ?? "", $code_data):
		case preg_match("/^([\d]{3})([\d]{2})([\d]{2})\-([\d]{3,4})$/", $data['utm_medium'] ?? "", $code_data):
		case preg_match("/^([\d]{3})([\d]{2})([\d]{2})\-([\d]{3,4})$/", $data['utm_campaign'] ?? "", $code_data):
			$campaign_term_code    = str_pad($code_data[4], 4, "0", STR_PAD_LEFT);
			$campaign_medium_code  = $code_data[2];
			$revenue_category_code = $code_data[3];
			break;

		case preg_match("/^([\d]{3})([\d]{1})([\d]{2})\-([\d]{4})$/", $data['utm_source'] ?? "", $code_data):
		case preg_match("/^([\d]{3})([\d]{1})([\d]{2})\-([\d]{4})$/", $data['ip_address'] ?? "", $code_data):
		case preg_match("/^([\d]{3})([\d]{1})([\d]{2})\-([\d]{4})$/", $data['utm_term'] ?? "", $code_data):
		case preg_match("/^([\d]{3})([\d]{1})([\d]{2})\-([\d]{4})$/", $data['utm_content'] ?? "", $code_data):
		case preg_match("/^([\d]{3})([\d]{1})([\d]{2})\-([\d]{4})$/", $data['utm_medium'] ?? "", $code_data):
		case preg_match("/^([\d]{3})([\d]{1})([\d]{2})\-([\d]{4})$/", $data['utm_campaign'] ?? "", $code_data):
			$campaign_term_code    = $code_data[4];
			$campaign_medium_code  = str_pad($code_data[2], 2, "0", STR_PAD_LEFT);
			$revenue_category_code = $code_data[3];
			break;

		default:

			$campaign_term_code    = null;
			$campaign_medium_code  = null;
			$revenue_category_code = null;

			switch($Lead->sub_type) {
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
			$Lead->campaign_term_id = \App\CampaignTerm::where("code", $campaign_term_code)->firstOrFail()->id;
		} catch (\Exception $e) {
			if(isset($data['utm_term']) && !in_array("{$campaign_term_code}-" . strtolower($data['utm_term']), $missing_terms)) {
				$missing_terms[] = "{$campaign_term_code}-" . strtolower($data['utm_term']);
			}
		}
	}

	if(isset($campaign_medium_code)) {
		try {
			$Lead->campaign_medium_id = \App\CampaignMedium::where("code", $campaign_medium_code)->firstOrFail()->id;
		} catch (\Exception $e) {}
	}

	if(isset($revenue_category_code)) {
		$Lead->revenue_category = (int)$revenue_category_code;
	}

	if(isset($data['utm_campaign'])) {
		try {
			$Lead->campaign_name_id = \App\CampaignName::where("slug", $data['utm_campaign'])->firstOrFail()->id;
		} catch (\Exception $e) {}
	}

	$Lead->save();

}

?>