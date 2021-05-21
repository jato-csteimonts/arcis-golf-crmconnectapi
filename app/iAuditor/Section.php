<?php

namespace App\iAuditor;

use Illuminate\Database\Eloquent\Model;
use Event;

class Section extends Model {

	protected $table = 'iauditor_audit_sections';
	protected $guarded = [];

	public function __construct() {
		parent::__construct();
	}

}
