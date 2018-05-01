<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserClub extends Model {
	protected $table = 'user_clubs';

	public function user() {
		return $this->hasOne('\App\User');
	}

	public function club() {
		return $this->hasOne('\App\Club');
	}
}
