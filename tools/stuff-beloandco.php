<?php

chdir(__DIR__);
require __DIR__ . '/../bootstrap/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
use Illuminate\Contracts\Console\Kernel;
$app->make(Kernel::class)->bootstrap();

$count = 0;

$missing_terms = [];

$sub_types = [];

foreach(App\Leads\Base::whereNull("duplicate_of")->whereNull("revenue_category")->where("type", App\Leads\Base::$TYPE_BELOANDCO)->orderBy("created_at", "DESC")->get() as $Lead) {

	$data = unserialize($Lead->data);

	$Lead->campaign_medium_id = 2;

	/**
	Array
	(
		[0] => private
	    [1] => wedding
		[2] => tournament
		[3] => corporate
		[4] => event
		[5] => member
	)
	**/

	$revenue_category = null;
	switch($data['sub_type']) {
		case "private":
		case "corporate":
		case "event":
		case "tournament":
			$revenue_category = 3;
			break;
		case "wedding": $revenue_category = 2; break;
		case "member": $revenue_category = 1; break;
	}

	if($revenue_category) {
		$Lead->revenue_category = $revenue_category;
	}

	$Lead->save();
}

?>