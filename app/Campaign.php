<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
	protected $guarded = [];
	protected $table = 'campaigns';

	public function campaign_medium() {
		return $this->hasOne('\App\CampaignMedium', "id", "campaign_medium_id");
	}

	public function club() {
		return $this->hasOne('\App\Club', "id", "club_id");
	}

}
