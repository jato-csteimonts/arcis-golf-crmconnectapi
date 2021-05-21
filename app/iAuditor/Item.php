<?php

namespace App\iAuditor;

use Illuminate\Database\Eloquent\Model;
use Event;

class Item extends Model {

	protected $table = 'iauditor_audit_items';
	protected $guarded = [];

	public function __construct() {
		parent::__construct();
	}

}
