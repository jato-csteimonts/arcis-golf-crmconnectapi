<?php

chdir(__DIR__);
require __DIR__ . '/../bootstrap/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
use Illuminate\Contracts\Console\Kernel;
$app->make(Kernel::class)->bootstrap();

$count = 0;

$missing_terms = [];

foreach(App\Leads\Base::whereNull("duplicate_of")->where("type", App\Leads\Base::$TYPE_FACEBOOK)->orderBy("created_at", "DESC")->get() as $Lead) {

	$data = unserialize($Lead->data);

	if(isset($data['utm_campaign'])) {
		try {
			$Lead->campaign_name_id = \App\CampaignName::where("slug", $data['utm_campaign'])->firstOrFail()->id;
		} catch (\Exception $e) {}
	}

	$Lead->save();

}

?>