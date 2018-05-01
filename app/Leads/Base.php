<?php

namespace App\Leads;

use Illuminate\Database\Eloquent\Model;

class Base extends Model {

	public static $TYPE_DISTRIBION = "distribion";
	public static $TYPE_UNBOUNCE   = "unbounce";

	protected $table = 'leads';

	public function __construct() {}

	public function normalize($data = []) {
		throw new \Exception("normalize() function should be implemented in child class (Class: {$this->type})");
	}

}