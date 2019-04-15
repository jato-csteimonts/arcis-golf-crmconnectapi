<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class UserClub extends Model {

	protected $table = 'user_clubs';
	protected $guarded = [];

	public function user() {
		return $this->hasOne('\App\User', 'user_id');
	}

	public function club() {
		return $this->hasOne('\App\Club', 'club_id');
	}
}
