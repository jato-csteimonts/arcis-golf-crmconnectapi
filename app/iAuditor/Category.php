<?php

namespace App\iAuditor;

use Illuminate\Database\Eloquent\Model;
use Event;

class Category extends Model {

	protected $table = 'iauditor_audit_categories';
	protected $guarded = [];

	public function __construct() {
		parent::__construct();
	}

}
