<?php

namespace App\Leads;

use Illuminate\Database\Eloquent\Model;
use Event;

class Base extends Model {

	public static $TYPE_DISTRIBION = "distribion";
	public static $TYPE_UNBOUNCE   = "unbounce";
	public static $TYPE_BELOANDCO  = "beloandco";
	public static $TYPE_FACEBOOK   = "facebook";

	protected $table = 'leads';

	public function __construct() {
		parent::__construct();
	}

	public function normalize($data = []) {
		throw new \Exception("normalize() function should be implemented in child class (Class: {$this->type})");
	}

	public static function boot() {
		parent::boot();
		static::created(function($item) {
			Event::fire('eloquent.created', $item);
		});
	}

	public function is_duplicate() {
		try {
			$dupe = Base::where("id", "!=", $this->id)
			            ->where("type", $this->type)
			            ->where("sub_type", $this->sub_type)
			            ->where("club_id", $this->club_id)
			            ->where("owner", $this->owner)
			            ->where("salesperson", $this->salesperson)
			            ->where("email", $this->email)
			            ->where("source", $this->source)
			            ->whereNull("duplicate_of")
						->whereRaw("TIMESTAMPDIFF(SECOND, `created_at`, '{$this->created_at}') <= 60")
			            ->orderBy("created_at", "DESC")
			            ->firstOrFail();
			return $dupe;
		} catch (\Exception $e) {
			return false;
		}
	}

}
