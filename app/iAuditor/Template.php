<?php

namespace App\iAuditor;

use Illuminate\Database\Eloquent\Model;
use Event;

class Template extends Model {

	protected $table   = 'iauditor_templates';
	protected $guarded = [];

	public function __construct() {
		parent::__construct();
	}

}
