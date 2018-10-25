<?php

namespace App\Leads;

use Illuminate\Database\Eloquent\Model;

class Base extends Model {

	public static $TYPE_DISTRIBION = "distribion";
	public static $TYPE_UNBOUNCE   = "unbounce";
	public static $TYPE_BELOANDCO  = "beloandco";
	public static $TYPE_FACEBOOK   = "facebook";

	protected $table = 'leads';

	public function __construct() {}

	public function normalize($data = []) {
		throw new \Exception("normalize() function should be implemented in child class (Class: {$this->type})");
	}

}
