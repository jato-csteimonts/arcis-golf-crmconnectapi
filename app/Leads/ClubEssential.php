<?php

namespace App\Leads;

class ClubEssential extends Base {

	public function __construct() {
		$this->setAttribute("type", self::$TYPE_CLUBESSENTIAL);
		parent::__construct();
	}

	public function normalize($data = []) {

		$out = [];
		return $out;

	}

}
