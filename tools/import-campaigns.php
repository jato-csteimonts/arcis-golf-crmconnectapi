<?php

chdir(__DIR__);
require __DIR__ . '/../bootstrap/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
use Illuminate\Contracts\Console\Kernel;
$app->make(Kernel::class)->bootstrap();


foreach(App\Leads\Base::get() as $Lead) {

	$data = unserialize($Lead->data);

	if(isset($data['utm_source']) && $data['utm_source'] &&
	   isset($data['utm_medium']) && $data['utm_medium'] &&
	   isset($data['utm_content']) && $data['utm_content'] &&
	   isset($data['utm_campaign']) && $data['utm_campaign'] &&
	   isset($data['utm_term']) && $data['utm_term']) {

		try {

			$CampaignMedium = App\CampaignMedium::where("slug", strtolower($data['utm_medium']))->firstOrFail();

			try {
				$Campaign = App\Campaign::where("club_id", $Lead->club_id)
				                        ->where("campaign_medium_id", $CampaignMedium->id)
				                        ->where("name", strtolower($data['utm_campaign']))
				                        ->where("term", strtolower($data['utm_term']))
				                        ->where("content", strtolower($data['utm_content']))
				                        ->firstOrFail();
			} catch (\Exception $e) {
				$Campaign = new App\Campaign();
				$Campaign->club_id = $Lead->club_id;
				$Campaign->campaign_medium_id = $CampaignMedium->id;
				$Campaign->name = strtolower($data['utm_campaign']);
				$Campaign->term = strtolower($data['utm_term']);
				$Campaign->content = strtolower($data['utm_content']);
				$Campaign->save();
				print("Created Campaign...\n");
			}

			if($Lead->campaign_id != $Campaign->id) {
				print("Assigning Campaign to Lead...\n");
				$Lead->campaign_id = $Campaign->id;
				$Lead->save();
			}

		} catch (\Exception $e) {}

	}
}

?>