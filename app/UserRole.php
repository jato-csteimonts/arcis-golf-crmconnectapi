<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserRole extends Model {
	protected $table = 'user_roles';

	public function user() {
		return $this->hasOne('\App\User');
	}

	public function club() {
		return $this->hasOne('\App\Club');
	}
}
