<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Club extends Model
{
	protected $guarded = [];
	use SoftDeletes;

	public function domains()
	{
		return $this->hasMany('App\Domain');
	}

	public function users()
	{
		return $this->belongsToMany('App\User', 'user_clubs', 'club_id')->orderBy("users.name", "ASC");
	}

	public function user_roles()
	{
		return $this->hasMany('App\UserRole', "club_id")->where("role", "owner");
	}

    public function digitalLeads()
    {
        return $this->hasMany('App\Digitallead');
    }

    public function websiteLeads()
    {
        return $this->hasMany('App\Websitelead');
    }

    public function adds()
    {
        return $this->hasMany('App\Add');
    }
}
