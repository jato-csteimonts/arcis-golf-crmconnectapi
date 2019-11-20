<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CampaignMedium extends Model
{
	protected $guarded = [];
	protected $table = 'campaign_mediums';

	public function campaigns()
	{
		return $this->hasMany('App\Campaign', "campaign_medium_id");
	}

}
