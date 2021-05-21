<?php

namespace App\iAuditor;

use Illuminate\Database\Eloquent\Model;
use Event;

class Audit extends Model {

	protected $table = 'iauditor_audits';
	protected $guarded = [];

	public function __construct() {
		parent::__construct();
	}

}
