<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Club extends Model
{
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
