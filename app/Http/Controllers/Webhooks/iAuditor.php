<?php

namespace App\Http\Controllers\Webhooks;

use App\Domain;
use App\Field;
use App\Webforms;
use App\Mail\Lead;

use Illuminate\Http\Request;

class iAuditor {

	public function get(Request $request) {




		\Log::info(print_r($request->toArray(),1));



	}

}
