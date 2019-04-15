<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
	protected $guarded = [];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function routeNotificationForSlack()
    {
        return 'https://hooks.slack.com/services/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';
    }

	public function clubs()
	{
		return $this->belongsToMany('App\Club', 'user_clubs', 'user_id')->orderBy("clubs.name", "ASC");
	}
}
