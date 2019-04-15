<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserRole extends Model {

	protected $table = 'user_roles';
	protected $guarded = [];

	public function user() {
		return $this->hasOne('\App\User', "id", "user_id");
	}

	public function club() {
		return $this->hasOne('\App\Club', "id", "club_id");
	}
}
